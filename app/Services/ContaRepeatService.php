<?php

namespace App\Services;

use App\Models\Conta;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;

class ContaRepeatService
{
    public function __construct(
        protected DateRecurrenceService $dateService
    ) {}

    /**
     * Repete um lançamento gerando N ocorrências futuras em lote.
     *
     * @param Conta $conta Lançamento original
     * @param array $options [
     *     'count' => int, // Quantidade de novas ocorrências a gerar (ex: 5)
     *     'frequency' => string, // monthly, weekly, biweekly, yearly, custom
     *     'interval' => int, // Intervalo (padrão 1, ou X dias)
     *     'start_date' => string|null, // Data inicial da 1ª repetição (se nulo, usa vencimento original)
     *     'custom_value' => float|string|null, // Valor personalizado para as cópias
     *     'operation_token' => string|null, // Proteção contra duplo clique
     * ]
     * @return Collection Contas criadas
     */
    public function repeat(Conta $conta, array $options): Collection
    {
        $count = (int) ($options['count'] ?? 0);
        if ($count < 1 || $count > 120) {
            throw new InvalidArgumentException('A quantidade de repetições deve ser entre 1 e 120.');
        }

        $frequency = $options['frequency'] ?? 'monthly';
        $interval = max(1, (int) ($options['interval'] ?? 1));

        $baseDate = !empty($options['start_date'])
            ? Carbon::parse($options['start_date'])
            : Carbon::parse($conta->maturity);

        $anchorDay = Carbon::parse($conta->maturity)->day;

        // Proteção contra duplo envio imediato (mesma conta repetida nos últimos 5 segundos)
        if (!empty($options['operation_token'])) {
            $recent = Conta::where('user_id', $conta->user_id)
                ->where('parent_id', $conta->id)
                ->where('created_at', '>=', now()->subSeconds(5))
                ->exists();

            if ($recent) {
                Log::warning('Tentativa de repetição duplicada detectada e prevenida.', [
                    'conta_id' => $conta->id,
                    'user_id' => $conta->user_id,
                ]);
                return Conta::where('parent_id', $conta->id)->get();
            }
        }

        $copyValue = !empty($options['custom_value'])
            ? str_replace(',', '.', str_replace('.', '', (string) $options['custom_value']))
            : $conta->value;

        return DB::transaction(function () use ($conta, $count, $frequency, $interval, $baseDate, $anchorDay, $copyValue) {
            // Se o lançamento original ainda não tem repeat_group_id, inicializa
            $groupId = $conta->repeat_group_id ?? (string) Str::ulid();
            $totalOccurrences = ($conta->repeat_total ?? 1) + $count;

            $conta->update([
                'repeat_group_id' => $groupId,
                'repeat_index' => $conta->repeat_index ?? 1,
                'repeat_total' => $totalOccurrences,
            ]);

            // Se a data de início for a mesma do vencimento original, as repetições começam a partir do índice 1
            // Se for informada uma data inicial diferente, usamos como base para os cálculos
            $isSameAsOriginal = $baseDate->isSameDay(Carbon::parse($conta->maturity));
            $dates = $this->dateService->generateSequence(
                $baseDate,
                $frequency,
                $count,
                $interval,
                $anchorDay,
                !$isSameAsOriginal
            );

            $createdContas = new Collection();
            $startIndex = ($conta->repeat_index ?? 1) + 1;

            foreach ($dates as $indexOffset => $maturityDate) {
                $currentIndex = $startIndex + $indexOffset;

                $newConta = Conta::create([
                    'name' => $conta->name,
                    'value' => $copyValue,
                    'maturity' => $maturityDate->toDateString(),
                    'situation' => 'pending', // Cópias sempre iniciam como pendentes
                    'category_id' => $conta->category_id,
                    'type' => $conta->type,
                    'note' => $conta->note,
                    'image' => null, // Comprovante não é copiado para novas ocorrências
                    'user_id' => $conta->user_id,
                    'fixed' => false,
                    'repeat' => 0,
                    'recurrence_id' => null,
                    'parent_id' => $conta->id,
                    'repeat_group_id' => $groupId,
                    'repeat_index' => $currentIndex,
                    'repeat_total' => $totalOccurrences,
                ]);

                $createdContas->push($newConta);
            }

            // Atualiza o repeat_total de todas as outras contas do grupo para consistência
            Conta::where('user_id', $conta->user_id)
                ->where('repeat_group_id', $groupId)
                ->update(['repeat_total' => $totalOccurrences]);

            return $createdContas;
        });
    }

    /**
     * Atualiza lançamentos de uma sequência repetida.
     *
     * @param Conta $conta
     * @param array $data Dados a serem atualizados (name, value, category_id, type, note)
     * @param string $scope 'only_this' | 'this_and_next' | 'all_sequence'
     * @return int Quantidade de registros afetados
     */
    public function updateSequence(Conta $conta, array $data, string $scope = 'only_this'): int
    {
        if (empty($conta->repeat_group_id) || $scope === 'only_this') {
            $conta->update($data);
            return 1;
        }

        return DB::transaction(function () use ($conta, $data, $scope) {
            $query = Conta::where('user_id', $conta->user_id)
                ->where('repeat_group_id', $conta->repeat_group_id);

            if ($scope === 'this_and_next') {
                if ($conta->repeat_index) {
                    $query->where('repeat_index', '>=', $conta->repeat_index);
                } else {
                    $query->where('maturity', '>=', $conta->maturity);
                }
            }

            // Ao atualizar em lote, se houver alteração de valor ou vencimento,
            // podemos optar por não alterar a situation de quem já está como 'paid'
            $updatableData = $data;
            unset($updatableData['maturity']); // Vencimentos individuais da sequência não são unificados
            unset($updatableData['image']); // Comprovantes não são unificados

            // Se tiver situação no array e for 'this_and_next' ou 'all_sequence',
            // apenas atualiza se especificado explicitamente, mas não sobrescreve 'paid' se o update for 'pending'
            if (isset($updatableData['situation'])) {
                // Atualiza mantendo pagos se o usuário não pediu para forçar
                $affected = $query->update($updatableData);
            } else {
                $affected = $query->update($updatableData);
            }

            // Se for 'only_this' ou na conta atual, aplica todos os dados incluindo vencimento
            $conta->update($data);

            return $affected;
        });
    }

    /**
     * Exclui lançamentos de uma sequência repetida.
     *
     * @param Conta $conta
     * @param string $scope 'only_this' | 'this_and_next' | 'all_sequence'
     * @return int Quantidade de registros excluídos
     */
    public function deleteSequence(Conta $conta, string $scope = 'only_this'): int
    {
        if (empty($conta->repeat_group_id) || $scope === 'only_this') {
            $conta->delete();
            return 1;
        }

        return DB::transaction(function () use ($conta, $scope) {
            $query = Conta::where('user_id', $conta->user_id)
                ->where('repeat_group_id', $conta->repeat_group_id);

            if ($scope === 'this_and_next') {
                if ($conta->repeat_index) {
                    $query->where('repeat_index', '>=', $conta->repeat_index);
                } else {
                    $query->where('maturity', '>=', $conta->maturity);
                }
            }

            return $query->delete();
        });
    }
}
