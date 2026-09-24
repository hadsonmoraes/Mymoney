<?php

namespace App\Http\Controllers;

use App\Models\Conta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ContaRequest;
use App\Http\Requests\RepeatContaRequest;
use App\Http\Requests\ConfigureInstallmentRequest;
use App\Models\Category;
use App\Models\Recurrence;
use App\Services\ContaRepeatService;
use App\Services\RecurrenceService;
use App\Services\InstallmentService;
use App\Services\ExcelImportService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {

        $user = auth()->user();

        $dataInicio = $request->filled('data_inicio') ? $request->data_inicio : Carbon::now()->startOfMonth()->format('Y-m-d');
        $dataFim = $request->filled('data_fim') ? $request->data_fim : Carbon::now()->endOfMonth()->format('Y-m-d');

        $perPage = $request->input('perPage', 10);

        $contasQuery = Conta::where('user_id', $user->id)
            ->when($request->has('name'), function ($whenQuery) use ($request) {
                $whenQuery->where('name', 'like', '%' . $request->name . '%');
            })
            ->where('maturity', '>=', Carbon::parse($dataInicio)->format('Y-m-d'))
            ->where('maturity', '<=', Carbon::parse($dataFim)->format('Y-m-d'))
            ->when($request->filled('situation'), function ($whenQuery) use ($request) {
                $whenQuery->where('situation', $request->situation);
            })
            ->when($request->filled('type'), function ($whenQuery) use ($request) {
                $whenQuery->where('type', $request->type);
            })
            ->orderByDesc('created_at');
        $allContas = $contasQuery->get();

        $contasEntrada = $allContas->where('type', 'entrada');
        $contasEntradaValor = $contasEntrada->sum('value');

        $contasSaida = $allContas->where('type', 'saida');
        $contasSaidaValor = $contasSaida->sum('value');
        $MyTotal = ($contasEntradaValor - $contasSaidaValor);
        $contas = $contasQuery->paginate($perPage)->withQueryString();
        session(['filtros_contas' => request()->query()]);

        return view('home', [
            'contas' => $contas,
            'name' => $request->name,
            'data_inicio' =>  $dataInicio,
            'data_fim' => $dataFim,
            'situation' => $request->situation,
            'type' => $request->type,
            'perPage' => $perPage,
            'entrada' => $contasEntradaValor,
            'saida' => $contasSaidaValor,
            'MyTotal' => $MyTotal,
        ]);
    }

    public function create()
    {
        $user = auth()->user();
        $categorys = Category::where('user_id', $user->id)->orderBy('name', 'asc')->get();

        return view('contas.create', [
            'categorys' => $categorys,
        ]);
    }

    public function store(ContaRequest $request, RecurrenceService $recurrenceService)
    {
        $request->validated();

        try {
            $contas = new Conta;

            $contas->name = $request->name;
            $contas->value = str_replace(',', '.', str_replace('.', '', $request->value));
            $contas->maturity = $request->maturity;
            $contas->situation = $request->situation;
            $contas->category_id = $request->category_id;
            $contas->type = $request->type;
            $contas->fixed = (bool) ($request->fixed ?? false);
            $contas->repeat = $request->repeat ?? 0;
            $contas->note = $request->note;

            if ($contas->value <= 0 || $contas->value === "") {
                return back()->withInput()->with('error', 'O valor precisa ser maior que zero');
            }

            $user = Auth::user();
            $contas->user_id = $user->id;

            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $requestImage = $request->image;
                $extension = $requestImage->extension();
                $imageName = md5($requestImage->getClientOriginalName() . strtotime("now")) . "." . $extension;
                $requestImage->move(public_path('img/comprovantes' . $contas->user_id), $imageName);
                $contas->image =  $imageName;
            }

            $contas->save();

            // Configurar regra de recorrência contínua se selecionada
            $recurrenceType = $request->input('recurrence_type');
            if ($recurrenceType && $recurrenceType !== 'none') {
                $recurrenceService->createFromConta($contas, [
                    'frequency' => $recurrenceType,
                    'interval' => $request->input('recurrence_interval', 1),
                    'end_date' => $request->input('recurrence_end_date'),
                    'max_occurrences' => $request->input('recurrence_max_occurrences'),
                    'start_date' => $contas->maturity,
                ]);
            } elseif ($contas->fixed) {
                // Compatibilidade com checkbox legado "Despesa/receita fixa"
                $recurrenceService->createFromConta($contas, [
                    'frequency' => 'monthly',
                    'interval' => 1,
                    'start_date' => $contas->maturity,
                ]);
            }

            return redirect()->route('home', session('filtros_contas'))->with('success', 'Conta criada com sucesso!');
        } catch (Exception $e) {
            Log::error('Conta não Cadastrada.', ['mensagem' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Conta não Cadastrada: ' . $e->getMessage());
        }
    }

    public function show($id, InstallmentService $installmentService)
    {
        $user = auth()->user();
        $contas = Conta::where('user_id', $user->id)->with(['recurrence', 'category'])->findOrFail($id);
        $categorys = Category::where('user_id', $user->id)->orderBy('name', 'asc')->get();

        $installmentSummary = $contas->installment_group_id
            ? $installmentService->getInstallmentSummary($contas->installment_group_id, $user->id)
            : null;

        $installmentPattern = !$contas->is_installment ? $contas->detectInstallmentPattern() : null;
        $potentialInstallments = ($installmentPattern)
            ? $installmentService->findPotentialInstallmentMatches($contas)
            : collect();

        return view('contas.show', [
            'contas' => $contas,
            'categorys' => $categorys,
            'installmentSummary' => $installmentSummary,
            'installmentPattern' => $installmentPattern,
            'potentialInstallments' => $potentialInstallments,
        ]);
    }

    public function edit($id, InstallmentService $installmentService)
    {
        $user = auth()->user();
        $contas = Conta::where('user_id', $user->id)->with(['recurrence', 'category'])->findOrFail($id);
        $categorys = Category::where('user_id', $user->id)->orderBy('name', 'asc')->get();

        $sequenceCount = 1;
        if (!empty($contas->repeat_group_id)) {
            $sequenceCount = Conta::where('user_id', $user->id)
                ->where('repeat_group_id', $contas->repeat_group_id)
                ->count();
        } elseif (!empty($contas->installment_group_id)) {
            $sequenceCount = Conta::where('user_id', $user->id)
                ->where('installment_group_id', $contas->installment_group_id)
                ->count();
        }

        $installmentSummary = $contas->installment_group_id
            ? $installmentService->getInstallmentSummary($contas->installment_group_id, $user->id)
            : null;

        $installmentPattern = !$contas->is_installment ? $contas->detectInstallmentPattern() : null;
        $potentialInstallments = ($installmentPattern)
            ? $installmentService->findPotentialInstallmentMatches($contas)
            : collect();

        return view('contas.edit', [
            'contas' => $contas,
            'categorys' => $categorys,
            'sequenceCount' => $sequenceCount,
            'installmentSummary' => $installmentSummary,
            'installmentPattern' => $installmentPattern,
            'potentialInstallments' => $potentialInstallments,
        ]);
    }

    public function update(ContaRequest $request, ContaRepeatService $repeatService, RecurrenceService $recurrenceService, InstallmentService $installmentService)
    {
        try {
            $user_id = auth()->user()->id;
            $data = $request->validated();
            $data['repeat'] = $request->repeat ?? 0;
            $data['note'] = $request->note;
            $data['value'] = str_replace(',', '.', str_replace('.', '', $request->value));
            if ($data['value'] <= 0 || $data['value'] === "") {
                return back()->withInput()->with('error', 'O valor precisa ser maior que zero');
            }
            $data['fixed'] = (bool) ($request->fixed ?? false);

            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $requestImage = $request->image;
                $extension = $requestImage->extension();
                $imageName = md5($requestImage->getClientOriginalName() . strtotime("now")) . "." . $extension;
                $requestImage->move(public_path('img/comprovantes' . $user_id), $imageName);
                $data['image'] =  $imageName;
            }

            $id = $request->id;
            $conta = Conta::where('user_id', $user_id)->findOrFail($id);

            // Remover campos extras que não pertencem à tabela contas
            $scope = $request->input('update_scope', 'only_this');
            unset($data['recurrence_type'], $data['recurrence_interval'], $data['recurrence_end_date'], $data['recurrence_max_occurrences'], $data['update_scope']);

            if (!empty($conta->repeat_group_id) && in_array($scope, ['this_and_next', 'all_sequence'])) {
                $repeatService->updateSequence($conta, $data, $scope);
            } elseif (!empty($conta->installment_group_id) && in_array($scope, ['this_and_next', 'all_sequence'])) {
                $installmentService->updateSequence($conta, $data, $scope);
            } else {
                $conta->update($data);
            }

            // Gerenciar regra de recorrência se alterada
            $recurrenceType = $request->input('recurrence_type');
            if ($recurrenceType && $recurrenceType !== 'none') {
                if ($conta->recurrence_id) {
                    $conta->recurrence->update([
                        'frequency' => $recurrenceType,
                        'interval' => $request->input('recurrence_interval', 1),
                        'end_date' => $request->input('recurrence_end_date'),
                        'max_occurrences' => $request->input('recurrence_max_occurrences'),
                        'name' => $conta->name,
                        'value' => $conta->value,
                        'category_id' => $conta->category_id,
                        'type' => $conta->type,
                        'note' => $conta->note,
                    ]);
                } else {
                    $recurrenceService->createFromConta($conta, [
                        'frequency' => $recurrenceType,
                        'interval' => $request->input('recurrence_interval', 1),
                        'end_date' => $request->input('recurrence_end_date'),
                        'max_occurrences' => $request->input('recurrence_max_occurrences'),
                        'start_date' => $conta->maturity,
                    ]);
                }
            }

            return redirect()->route('home', session('filtros_contas'))->with('success', 'Conta atualizada com sucesso!');
        } catch (Exception $e) {
            Log::error('Conta não atualizada.', ['mensagem' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Conta não atualizada: ' . $e->getMessage());
        }
    }

    public function repeat(RepeatContaRequest $request, $id, ContaRepeatService $repeatService)
    {
        $user_id = auth()->user()->id;
        $conta = Conta::where('user_id', $user_id)->findOrFail($id);

        try {
            $created = $repeatService->repeat($conta, $request->validated());
            $count = $created->count();

            return redirect()->route('home', session('filtros_contas'))
                ->with('success', "Lançamento repetido com sucesso! {$count} nova(s) ocorrência(s) gerada(s).");
        } catch (Exception $e) {
            Log::error('Erro ao repetir lançamento.', ['mensagem' => $e->getMessage()]);
            return back()->with('error', 'Não foi possível repetir o lançamento: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, $id, ContaRepeatService $repeatService, RecurrenceService $recurrenceService, InstallmentService $installmentService)
    {
        $conta = Conta::where('user_id', auth()->id())->findOrFail($id);
        $scope = $request->input('delete_scope', 'only_this');
        $cancelRecurrence = $request->boolean('cancel_recurrence', false);

        if ($cancelRecurrence && $conta->recurrence_id) {
            $recurrenceService->cancelFromConta($conta);
        }

        if (!empty($conta->repeat_group_id) && in_array($scope, ['this_and_next', 'all_sequence'])) {
            $repeatService->deleteSequence($conta, $scope);
            $msg = $scope === 'all_sequence' ? 'Toda a sequência foi apagada!' : 'Este e os lançamentos posteriores foram apagados!';
        } elseif (!empty($conta->installment_group_id) && in_array($scope, ['this_and_next', 'all_sequence'])) {
            $installmentService->deleteSequence($conta, $scope);
            $msg = $scope === 'all_sequence' ? 'Todas as parcelas foram apagadas!' : 'Esta e as parcelas posteriores foram apagadas!';
        } else {
            $conta->delete();
            $msg = 'Conta apagada!';
        }

        return redirect()->route('home', session('filtros_contas'))->with('success', $msg);
    }

    public function configureInstallment(ConfigureInstallmentRequest $request, $id, InstallmentService $installmentService)
    {
        $conta = Conta::where('user_id', auth()->id())->findOrFail($id);

        try {
            $validated = $request->validated();
            if ($validated['is_installment']) {
                $installmentService->configureInstallment($conta, $validated);
                return back()->with('success', 'Parcelamento estruturado configurado com sucesso!');
            } else {
                $conta->update([
                    'installment_group_id' => null,
                    'installment_number' => null,
                    'installments_total' => null,
                ]);
                return back()->with('success', 'Configuração de parcelamento removida com sucesso!');
            }
        } catch (Exception $e) {
            Log::error('Erro ao configurar parcelamento', ['error' => $e->getMessage()]);
            return back()->with('error', 'Erro ao configurar parcelamento: ' . $e->getMessage());
        }
    }

    public function installmentDetails($id, InstallmentService $installmentService)
    {
        $conta = Conta::where('user_id', auth()->id())->findOrFail($id);
        if (!$conta->installment_group_id) {
            return response()->json(['error' => 'Lançamento não possui parcelamento estruturado.'], 404);
        }

        $summary = $installmentService->getInstallmentSummary($conta->installment_group_id, auth()->id());
        return response()->json($summary);
    }

    public function downloadImportTemplate(ExcelImportService $excelService)
    {
        $csvContent = $excelService->generateTemplateCsv();
        return response($csvContent, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="modelo_importacao_contas.csv"',
        ]);
    }

    public function importExcelPreview(Request $request, ExcelImportService $excelService)
    {
        $request->validate([
            'import_file' => 'required|file|max:10240',
        ], [
            'import_file.required' => 'Selecione um arquivo Excel (.xlsx) ou CSV para importar.',
            'import_file.max' => 'O arquivo não pode exceder 10MB.',
        ]);

        try {
            $file = $request->file('import_file');
            $analysis = $excelService->parseAndValidate($file->getRealPath(), auth()->id());

            session(['excel_import_token' => $analysis['import_token']]);

            return response()->json([
                'success' => true,
                'data' => $analysis,
            ]);
        } catch (Exception $e) {
            Log::error('Erro na pré-visualização do Excel', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar arquivo: ' . $e->getMessage(),
            ], 422);
        }
    }

    public function importExcelConfirm(Request $request, ExcelImportService $excelService)
    {
        $token = $request->input('import_token') ?: session('excel_import_token');

        if (empty($token)) {
            return back()->with('error', 'Nenhum dado válido para importação encontrado na sessão. Por favor envie o arquivo novamente.');
        }

        try {
            $autoCreateCategories = $request->boolean('auto_create_categories', false);
            $imported = $excelService->commitImport($token, auth()->id(), ['create_missing_categories' => $autoCreateCategories]);

            session()->forget('excel_import_token');

            return redirect()->route('home')->with('success', "Importação concluída com sucesso! {$imported} lançamentos foram criados.");
        } catch (Exception $e) {
            Log::error('Erro ao confirmar importação do Excel', ['error' => $e->getMessage()]);
            return back()->with('error', 'Erro durante a importação: ' . $e->getMessage());
        }
    }

    public function cancelFixed($id, RecurrenceService $recurrenceService)
    {
        $conta = Conta::where('user_id', auth()->id())->findOrFail($id);

        if (!$conta->fixed && !$conta->recurrence_id) {
            return back()->with('error', 'Esta conta não possui recorrência fixa.');
        }

        $recurrenceService->cancelFromConta($conta);

        return back()->with('success', 'Recorrência cancelada. Os lançamentos existentes foram mantidos.');
    }

    public function changeSituation(Conta $id)
    {

        try {
            $conta = Conta::where('user_id', auth()->id())->findOrFail($id->id);

            if ($conta->situation === 'paid') {
                $novaSituacao = 'pending';
            } elseif ($conta->situation === 'pending') {
                $novaSituacao = 'canceled';
            } else {
                $novaSituacao = 'paid';
            }

            $conta->update(['situation' => $novaSituacao]);
            return back()->withInput()->with('success', 'Situaçao da conta editada com sucesso!');
        } catch (Exception $e) {
            Log::error('Situaçao da conta não editada', ['mensagem' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Situaçao da conta não editada');
        }
    }

    public function gerarCsv(Request $request)
    {
        $user = auth()->user();

        $dataInicio = $request->filled('data_inicio') ? $request->data_inicio : Carbon::now()->startOfMonth()->format('Y-m-d');
        $dataFim = $request->filled('data_fim') ? $request->data_fim : Carbon::now()->endOfMonth()->format('Y-m-d');

        $perPage = $request->input('perPage', 5);

        $contasQuery = Conta::where('user_id', $user->id)
            ->when($request->has('name'), function ($whenQuery) use ($request) {
                $whenQuery->where('name', 'like', '%' . $request->name . '%');
            })
            ->where('maturity', '>=', Carbon::parse($dataInicio)->format('Y-m-d'))
            ->where('maturity', '<=', Carbon::parse($dataFim)->format('Y-m-d'))
            ->when($request->filled('situation'), function ($whenQuery) use ($request) {
                $whenQuery->where('situation', $request->situation);
            })
            ->when($request->filled('type'), function ($whenQuery) use ($request) {
                $whenQuery->where('type', $request->type);
            })
            ->orderByDesc('created_at');

        $contas = $contasQuery->paginate($perPage)->withQueryString();

        $totalValor = $contas->sum('value');

        // Criar o arquivo temporário
        $csvNomeArquivo = tempnam(sys_get_temp_dir(), 'csv_' . Str::ulid());

        // Abrir o arquivo na forma de escrita
        $arquivoAberto = fopen($csvNomeArquivo, 'w');

        // Criar o cabeçalho do Excel - Usar a função mb_convert_encoding para converter carateres especiais
        $cabecalho = ['id', 'Nome', 'Vencimento', mb_convert_encoding('Situação', 'ISO-8859-1', 'UTF-8'), 'Categoria', 'Tipo', 'Valor'];

        // Escrever o cabeçalho no arquivo
        fputcsv($arquivoAberto, $cabecalho, ';');

        // Ler os registros recuperados do banco de dados
        foreach ($contas as $conta) {

            $situation = $conta->situation;
            if ($situation == "paid") {
                $situation = "Pago";
            } elseif ($situation == "pending") {
                $situation = "Pendente";
            } else {
                $situation = "Cancelado";
            }

            // Criar o array com os dados da linha do Excel
            $contaArray = [
                'id' => $conta->id,
                'nome' => mb_convert_encoding($conta->name, 'ISO-8859-1', 'UTF-8'),
                'vencimento' => date('d/m/Y', strtotime($conta->maturity)),
                'situacao' => mb_convert_encoding($situation, 'ISO-8859-1', 'UTF-8'),
                'categoria' => mb_convert_encoding($conta->category->name, 'ISO-8859-1', 'UTF-8'),
                'tipo' => mb_convert_encoding($conta->type, 'ISO-8859-1', 'UTF-8'),
                'valor' => number_format($conta->value, 2, ',', '.'),
            ];

            // Escrever o conteúdo no arquivo
            fputcsv($arquivoAberto, $contaArray, ';');
        }


        // Criar o rodapé do Excel
        $rodape = ['', '', '', '', '', '', number_format($totalValor, 2, ',', '.')];

        // Escrever o conteúdo no arquivo
        fputcsv($arquivoAberto, $rodape, ';');

        // Fechar o arquivo após a escrita
        fclose($arquivoAberto);

        // Realizar o download do arquivo
        return response()->download($csvNomeArquivo, 'relatorio_contas_' . Str::ulid() . '.csv');
    }
}
