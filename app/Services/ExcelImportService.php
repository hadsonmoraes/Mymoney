<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Conta;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use ZipArchive;

class ExcelImportService
{
    /**
     * Gera o conteúdo CSV do modelo de importação.
     */
    public function generateTemplateCsv(): string
    {
        $headers = [
            'Data',
            'Descrição',
            'Tipo',
            'Valor',
            'Categoria',
            'Situação',
            'Observação',
            'Parcela',
            'Total de Parcelas',
        ];

        $sampleRows = [
            ['10/10/2026', 'Internet Fibra', 'Saída', '120,00', 'Moradia', 'Pendente', 'Boleto mensal', '', ''],
            ['15/10/2026', 'Notebook Dell', 'Saída', '350,00', 'Eletrônicos', 'Pendente', 'Compra parcelada', '3', '12'],
            ['05/10/2026', 'Salário', 'Entrada', '4.500,00', 'Rendimento', 'Pago', 'Salário mensal', '', ''],
        ];

        $output = fopen('php://temp', 'r+');
        // Adiciona BOM UTF-8 para Excel abrir com acentuação correta
        fputs($output, "\xEF\xBB\xBF");
        fputcsv($output, $headers, ';');

        foreach ($sampleRows as $row) {
            fputcsv($output, $row, ';');
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }

    /**
     * Lê, valida e pré-visualiza os dados de uma planilha sem gravar no banco.
     */
    public function parseAndValidate(UploadedFile|string $file, int $userId, array $options = []): array
    {
        $filePath = $file instanceof UploadedFile ? $file->getRealPath() : $file;
        $extension = strtolower($file instanceof UploadedFile ? $file->getClientOriginalExtension() : pathinfo($filePath, PATHINFO_EXTENSION));

        $rawRows = match ($extension) {
            'xlsx' => $this->parseXlsxFile($filePath),
            default => $this->parseCsvFile($filePath),
        };

        if (empty($rawRows)) {
            return [
                'total_rows' => 0,
                'valid_count' => 0,
                'invalid_count' => 0,
                'valid_rows' => [],
                'invalid_rows' => [['line' => 1, 'errors' => ['O arquivo está vazio ou com formato ilegível.']]],
                'duplicates_warning' => [],
                'missing_categories' => [],
                'import_token' => null,
            ];
        }

        // Carrega categorias do usuário para busca rápida em memória
        $userCategories = Category::where('user_id', $userId)->get()->keyBy(fn($c) => mb_strtolower(trim($c->name)));

        $validRows = [];
        $invalidRows = [];
        $duplicatesWarning = [];
        $missingCategories = [];

        // Identifica e descarta cabeçalho
        $firstRow = $rawRows[0];
        $startIndex = 0;
        $headerMap = null;
        if ($this->isHeaderRow($firstRow)) {
            $startIndex = 1;
            $headerMap = $this->detectHeaderMap($firstRow);
        }

        for ($i = $startIndex; $i < count($rawRows); $i++) {
            $rowNumber = $i + 1;
            $row = $rawRows[$i];

            if ($this->isEmptyRow($row)) {
                continue;
            }

            $validation = $this->validateRow($row, $rowNumber, $userId, $userCategories, $headerMap);

            if (!empty($validation['missing_category'])) {
                $missingCategories[$validation['missing_category']] = true;
            }

            if (empty($validation['errors'])) {
                $validRows[] = $validation['data'];

                // Verifica duplicidade no banco
                $isDuplicate = Conta::where('user_id', $userId)
                    ->where('maturity', $validation['data']['maturity'])
                    ->where('name', $validation['data']['name'])
                    ->where('value', $validation['data']['value'])
                    ->exists();

                if ($isDuplicate) {
                    $duplicatesWarning[] = [
                        'row' => $rowNumber,
                        'line' => $rowNumber,
                        'name' => $validation['data']['name'],
                        'value' => $validation['data']['value'],
                        'maturity' => $validation['data']['maturity'],
                        'message' => "Possível lançamento duplicado: '{$validation['data']['name']}' em {$validation['data']['maturity']}.",
                    ];
                }
            } else {
                $invalidRows[] = [
                    'row' => $rowNumber,
                    'line' => $rowNumber,
                    'raw' => implode(' | ', array_filter($row)),
                    'errors' => $validation['errors'],
                    'message' => implode(', ', $validation['errors']),
                ];
            }
        }

        $importToken = (string) Str::ulid();
        Cache::put("excel_import_{$userId}_{$importToken}", [
            'valid_rows' => $validRows,
            'user_id' => $userId,
        ], now()->addMinutes(60));

        return [
            'total_rows' => count($validRows) + count($invalidRows),
            'valid_count' => count($validRows),
            'valid_rows' => count($validRows),
            'invalid_count' => count($invalidRows),
            'errors_count' => count($invalidRows),
            'warnings_count' => count($duplicatesWarning),
            'rows' => $validRows,
            'errors' => $invalidRows,
            'warnings' => $duplicatesWarning,
            'duplicates_warning' => $duplicatesWarning,
            'missing_categories' => array_keys($missingCategories),
            'import_token' => $importToken,
        ];
    }

    /**
     * Grava definitivamente os registros válidos no banco com transação atômica.
     */
    public function commitImport(string $importToken, int $userId, array $options = []): int
    {
        $cacheKey = "excel_import_{$userId}_{$importToken}";
        $data = Cache::get($cacheKey);

        if (!$data || $data['user_id'] !== $userId) {
            throw new Exception('Sessão de importação expirada ou inválida. Por favor, envie o arquivo novamente.');
        }

        $validRows = $data['valid_rows'];
        if (empty($validRows)) {
            return 0;
        }

        $createMissingCategories = !empty($options['create_missing_categories']);

        return DB::transaction(function () use ($validRows, $userId, $createMissingCategories, $cacheKey) {
            $userCategories = Category::where('user_id', $userId)->get()->keyBy(fn($c) => mb_strtolower(trim($c->name)));
            $installmentGroups = [];
            $importedCount = 0;

            foreach ($validRows as $row) {
                $catName = mb_strtolower(trim($row['category_name']));
                $categoryId = null;

                if (isset($userCategories[$catName])) {
                    $categoryId = $userCategories[$catName]->id;
                } elseif ($createMissingCategories && !empty($row['category_name'])) {
                    $newCat = Category::create([
                        'name' => $row['category_name'],
                        'user_id' => $userId,
                    ]);
                    $userCategories[$catName] = $newCat;
                    $categoryId = $newCat->id;
                }

                // Identificador de parcelamento compartilhado quando há número e total
                $installmentGroupId = null;
                if (!empty($row['installment_number']) && !empty($row['installments_total'])) {
                    $baseNameKey = mb_strtolower(trim(preg_replace('/(?:\s*\-\s*)?\d{1,3}\s*\/\s*\d{1,3}/i', '', $row['name']))) . '_' . $row['installments_total'];
                    if (!isset($installmentGroups[$baseNameKey])) {
                        $installmentGroups[$baseNameKey] = (string) Str::ulid();
                    }
                    $installmentGroupId = $installmentGroups[$baseNameKey];
                }

                Conta::create([
                    'name' => $row['name'],
                    'value' => $row['value'],
                    'maturity' => $row['maturity'],
                    'situation' => $row['situation'],
                    'category_id' => $categoryId,
                    'type' => $row['type'],
                    'note' => $row['note'] ?? null,
                    'user_id' => $userId,
                    'fixed' => false,
                    'repeat' => 0,
                    'installment_group_id' => $installmentGroupId,
                    'installment_number' => $row['installment_number'] ?? null,
                    'installments_total' => $row['installments_total'] ?? null,
                ]);

                $importedCount++;
            }

            Cache::forget($cacheKey);
            return $importedCount;
        });
    }

    /**
     * Valida os campos de uma única linha da planilha.
     */
    protected function validateRow(array $row, int $line, int $userId, $userCategories, ?array $headerMap = null): array
    {
        $errors = [];
        $missingCategory = null;

        // 1. Data
        $rawDate = isset($headerMap['date']) ? trim($row[$headerMap['date']] ?? '') : trim($row[0] ?? '');
        $date = $this->parseDate($rawDate);
        if (!$date) {
            $errors[] = "Data inválida ou vazia ('{$rawDate}'). Utilize o formato DD/MM/AAAA.";
        }

        // 2. Descrição / Nome
        $name = isset($headerMap['name']) ? trim($row[$headerMap['name']] ?? '') : trim($row[1] ?? '');
        if ($name === '') {
            $errors[] = "Descrição/Nome é obrigatório.";
        }

        // 3. Tipo
        $rawType = isset($headerMap['type']) ? mb_strtolower(trim($row[$headerMap['type']] ?? '')) : mb_strtolower(trim($row[2] ?? ''));
        $type = match ($rawType) {
            'entrada', 'receita', 'in', 'crédito', 'credito' => 'entrada',
            'saida', 'saída', 'despesa', 'out', 'débito', 'debito' => 'saida',
            default => null,
        };
        if (!$type) {
            $errors[] = "Tipo inválido ('{$rawType}'). Informe 'Entrada' ou 'Saída'.";
        }

        // 4. Valor
        $rawValue = isset($headerMap['value']) ? trim($row[$headerMap['value']] ?? '') : trim($row[3] ?? '');
        $value = $this->parseMoney($rawValue);
        if ($value === null || $value <= 0) {
            $errors[] = "Valor inválido ('{$rawValue}'). O valor deve ser maior que zero.";
        }

        // 5. Categoria
        $categoryName = isset($headerMap['category']) ? trim($row[$headerMap['category']] ?? '') : trim($row[4] ?? '');
        if ($categoryName !== '') {
            $catLower = mb_strtolower($categoryName);
            if (!isset($userCategories[$catLower])) {
                $missingCategory = $categoryName;
            }
        }

        // 6. Situação / Status
        $rawSituation = isset($headerMap['situation']) ? mb_strtolower(trim($row[$headerMap['situation']] ?? '')) : mb_strtolower(trim($row[5] ?? ''));
        $situation = match ($rawSituation) {
            'pago', 'paga', 'paid' => 'paid',
            'cancelado', 'cancelada', 'canceled' => 'canceled',
            default => 'pending',
        };

        // 7. Observação
        $note = isset($headerMap['note']) ? trim($row[$headerMap['note']] ?? '') : trim($row[6] ?? '');

        // 8. Parcela e Total
        $rawInstNumber = isset($headerMap['installment_number']) ? ($row[$headerMap['installment_number']] ?? '') : ($row[7] ?? '');
        $rawInstTotal = isset($headerMap['installments_total']) ? ($row[$headerMap['installments_total']] ?? '') : ($row[8] ?? '');

        $installmentNumber = !empty($rawInstNumber) && is_numeric(trim($rawInstNumber)) ? (int) trim($rawInstNumber) : null;
        $installmentsTotal = !empty($rawInstTotal) && is_numeric(trim($rawInstTotal)) ? (int) trim($rawInstTotal) : null;

        if ($installmentNumber && $installmentsTotal && $installmentNumber > $installmentsTotal) {
            $errors[] = "O número da parcela ({$installmentNumber}) não pode ser maior que o total ({$installmentsTotal}).";
        }

        return [
            'errors' => $errors,
            'missing_category' => $missingCategory,
            'data' => [
                'row' => $line,
                'name' => $name,
                'value' => $value !== null ? number_format($value, 2, '.', '') : '0.00',
                'maturity' => $date ? $date->toDateString() : null,
                'type' => $type,
                'situation' => $situation,
                'category_name' => $categoryName,
                'category' => $categoryName,
                'note' => $note ?: null,
                'installment_number' => $installmentNumber,
                'installments_total' => $installmentsTotal,
            ],
        ];
    }

    /**
     * Mapeia automaticamente as colunas pelo nome do cabeçalho.
     */
    protected function detectHeaderMap(array $row): ?array
    {
        $map = [];
        foreach ($row as $index => $cell) {
            $norm = mb_strtolower(trim((string) $cell));
            if (str_contains($norm, 'data') || str_contains($norm, 'venc')) {
                $map['date'] = $index;
            } elseif (str_contains($norm, 'nome') || str_contains($norm, 'descri') || str_contains($norm, 'título')) {
                $map['name'] = $index;
            } elseif (str_contains($norm, 'tipo')) {
                $map['type'] = $index;
            } elseif (str_contains($norm, 'valor')) {
                $map['value'] = $index;
            } elseif (str_contains($norm, 'categ')) {
                $map['category'] = $index;
            } elseif (str_contains($norm, 'situa') || str_contains($norm, 'status')) {
                $map['situation'] = $index;
            } elseif (str_contains($norm, 'nota') || str_contains($norm, 'observa')) {
                $map['note'] = $index;
            } elseif (str_contains($norm, 'total')) {
                $map['installments_total'] = $index;
            } elseif (str_contains($norm, 'parcela')) {
                $map['installment_number'] = $index;
            }
        }

        return !empty($map) ? $map : null;
    }

    /**
     * Converte datas em diversos formatos (brasileiro DD/MM/AAAA, AAAA-MM-DD, números seriais do Excel).
     */
    public function parseDate(string $raw): ?Carbon
    {
        $raw = trim($raw);
        if ($raw === '') {
            return null;
        }

        // Se for número serial de data do Excel (ex: 45678)
        if (is_numeric($raw) && (int) $raw > 20000 && (int) $raw < 70000) {
            return Carbon::createFromTimestampUTC(((int) $raw - 25569) * 86400);
        }

        // Formatos comuns
        $formats = ['d/m/Y', 'd-m-Y', 'Y-m-d', 'd/m/y', 'Y/m/d'];
        foreach ($formats as $format) {
            try {
                $d = Carbon::createFromFormat($format, $raw);
                if ($d && $d->year >= 2000 && $d->year <= 2100) {
                    return $d;
                }
            } catch (Exception) {
                // Tenta próximo formato
            }
        }

        try {
            return Carbon::parse($raw);
        } catch (Exception) {
            return null;
        }
    }

    /**
     * Converte valores monetários brasileiros para float.
     * Suporta: "R$ 1.377,37", "1.377,37", "1377,37", "1377.37".
     */
    public function parseMoney(string $raw): ?float
    {
        $clean = trim($raw);
        if ($clean === '') {
            return null;
        }

        $clean = preg_replace('/[^\d,\.]/', '', $clean);

        // Se contiver vírgula, tratamos como separador decimal brasileiro
        if (str_contains($clean, ',')) {
            $clean = str_replace('.', '', $clean);
            $clean = str_replace(',', '.', $clean);
        }

        if (!is_numeric($clean)) {
            return null;
        }

        return (float) $clean;
    }

    /**
     * Lê arquivo CSV com detecção automática de codificação e delimitador (; ou ,).
     */
    protected function parseCsvFile(string $filePath): array
    {
        $content = file_get_contents($filePath);
        if ($content === false) {
            return [];
        }

        // Converte encoding para UTF-8 se necessário
        $encoding = mb_detect_encoding($content, ['UTF-8', 'ISO-8859-1', 'Windows-1252', 'ASCII'], true);
        if ($encoding && $encoding !== 'UTF-8') {
            $content = mb_convert_encoding($content, 'UTF-8', $encoding);
        }

        // Remove UTF-8 BOM se presente
        $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);

        // Detecta delimitador na primeira linha
        $firstLine = strtok($content, "\r\n");
        $delimiter = (substr_count($firstLine, ';') >= substr_count($firstLine, ',')) ? ';' : ',';

        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $content);
        rewind($stream);

        $rows = [];
        while (($row = fgetcsv($stream, 0, $delimiter)) !== false) {
            $rows[] = $row;
        }

        fclose($stream);
        return $rows;
    }

    /**
     * Lê arquivo .xlsx nativamente via ZipArchive e XMLReader/SimpleXML.
     */
    protected function parseXlsxFile(string $filePath): array
    {
        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            return [];
        }

        // 1. Ler sharedStrings.xml se existir
        $sharedStrings = [];
        $stringsXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($stringsXml) {
            $xml = simplexml_load_string($stringsXml);
            if ($xml && isset($xml->si)) {
                foreach ($xml->si as $si) {
                    $sharedStrings[] = (string) ($si->t ?? (isset($si->r) ? implode('', (array) $si->r) : ''));
                }
            }
        }

        // 2. Ler primeira planilha (xl/worksheets/sheet1.xml)
        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();

        if (!$sheetXml) {
            return [];
        }

        $xml = simplexml_load_string($sheetXml);
        if (!$xml || !isset($xml->sheetData->row)) {
            return [];
        }

        $rows = [];
        foreach ($xml->sheetData->row as $row) {
            $parsedRow = [];
            $colIndex = 0;

            foreach ($row->c as $cell) {
                // Determina índice de coluna pela referência da célula (ex: A1, B1, C1...)
                $cellRef = (string) $cell['r'];
                preg_match('/^([A-Z]+)/', $cellRef, $matches);
                $targetCol = $this->letterToColumnIndex($matches[1] ?? 'A');

                while ($colIndex < $targetCol) {
                    $parsedRow[] = '';
                    $colIndex++;
                }

                $type = (string) $cell['t'];
                $val = (string) $cell->v;

                if ($type === 's' && isset($sharedStrings[(int) $val])) {
                    $cellValue = $sharedStrings[(int) $val];
                } else {
                    $cellValue = $val;
                }

                $parsedRow[] = $cellValue;
                $colIndex++;
            }

            $rows[] = $parsedRow;
        }

        return $rows;
    }

    protected function letterToColumnIndex(string $letters): int
    {
        $num = 0;
        $len = strlen($letters);
        for ($i = 0; $i < $len; $i++) {
            $num = $num * 26 + (ord($letters[$i]) - ord('A') + 1);
        }
        return $num - 1;
    }

    protected function isHeaderRow(array $row): bool
    {
        $joined = mb_strtolower(implode(';', $row));
        return (str_contains($joined, 'data') || str_contains($joined, 'venc'))
            && (str_contains($joined, 'nome') || str_contains($joined, 'descri'))
            || (str_contains($joined, 'valor') && str_contains($joined, 'tipo'));
    }

    protected function isEmptyRow(array $row): bool
    {
        return empty(array_filter($row, fn($cell) => trim((string) $cell) !== ''));
    }
}
