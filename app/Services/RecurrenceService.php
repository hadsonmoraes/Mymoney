<?php

namespace App\Services;

use App\Models\Conta;
use App\Models\Recurrence;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RecurrenceService
{
    public function __construct(
        protected DateRecurrenceService $dateService
    ) {}

    /**
     * Cria uma regra de recorrência a partir de um lançamento base.
     */
    public function createFromConta(Conta $conta, array $data): Recurrence
    {
        $frequency = $data['frequency'] ?? 'monthly';
        $interval = max(1, (int) ($data['interval'] ?? 1));
        $startDate = !empty($data['start_date']) ? Carbon::parse($data['start_date']) : Carbon::parse($conta->maturity);
        $anchorDay = $startDate->day;

        // A primeira ocorrência já é o próprio $conta. O next_run_date é a próxima data calculada.
        $nextRunDate = $this->dateService->getNextDate($startDate, $frequency, $interval, $anchorDay);

        return DB::transaction(function () use ($conta, $data, $frequency, $interval, $startDate, $anchorDay, $nextRunDate) {
            $recurrence = Recurrence::create([
                'user_id' => $conta->user_id,
                'category_id' => $conta->category_id,
                'name' => $conta->name,
                'value' => $conta->value,
                'type' => $conta->type,
                'frequency' => $frequency,
                'interval' => $interval,
                'anchor_day' => $anchorDay,
                'start_date' => $startDate->toDateString(),
                'end_date' => !empty($data['end_date']) ? Carbon::parse($data['end_date'])->toDateString() : null,
                'max_occurrences' => !empty($data['max_occurrences']) ? (int) $data['max_occurrences'] : null,
                'occurrences_count' => 1, // Já conta o lançamento inicial
                'next_run_date' => $nextRunDate->toDateString(),
                'last_generated_at' => now(),
                'status' => 'active',
                'note' => $conta->note,
            ]);

            $conta->update([
                'recurrence_id' => $recurrence->id,
                'fixed' => true,
            ]);

            return $recurrence;
        });
    }

    /**
     * Processa todas as recorrências ativas com vencimento até $targetDate.
     */
    public function processDueRecurrences(?string $targetDate = null): int
    {
        $date = $targetDate ? Carbon::parse($targetDate)->toDateString() : now()->toDateString();
        $recurrences = Recurrence::where('status', 'active')
            ->where('next_run_date', '<=', $date)
            ->get();

        $generatedCount = 0;

        foreach ($recurrences as $recurrence) {
            $generated = $this->processRecurrence($recurrence, $date);
            if ($generated) {
                $generatedCount++;
            }
        }

        return $generatedCount;
    }

    /**
     * Processa recorrências de um usuário específico (ex: pós-login ou manual).
     */
    public function processForUser(User $user, ?string $targetDate = null): int
    {
        $date = $targetDate ? Carbon::parse($targetDate)->toDateString() : now()->toDateString();
        $recurrences = Recurrence::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('next_run_date', '<=', $date)
            ->get();

        $generatedCount = 0;

        foreach ($recurrences as $recurrence) {
            $generated = $this->processRecurrence($recurrence, $date);
            if ($generated) {
                $generatedCount++;
            }
        }

        return $generatedCount;
    }

    /**
     * Processa uma única recorrência gerando lançamentos pendentes até a data alvo.
     */
    public function processRecurrence(Recurrence $recurrence, string $targetDate): bool
    {
        if ($recurrence->status !== 'active') {
            return false;
        }

        $hasGeneratedAny = false;

        // Loop para caso haja múltiplas ocorrências acumuladas até a data alvo
        while ($recurrence->next_run_date->lte(Carbon::parse($targetDate))) {
            // Verificar limite de ocorrências
            if ($recurrence->max_occurrences && $recurrence->occurrences_count >= $recurrence->max_occurrences) {
                $recurrence->update(['status' => 'completed']);
                break;
            }

            // Verificar data limite final
            if ($recurrence->end_date && $recurrence->next_run_date->gt($recurrence->end_date)) {
                $recurrence->update(['status' => 'completed']);
                break;
            }

            $currentDueDate = $recurrence->next_run_date->copy();

            // Proteção contra duplicidade: verifica se já existe conta vinculada a essa recorrência nessa data
            $alreadyExists = Conta::where('recurrence_id', $recurrence->id)
                ->whereDate('maturity', $currentDueDate->toDateString())
                ->exists();

            if (!$alreadyExists) {
                DB::transaction(function () use ($recurrence, $currentDueDate) {
                    Conta::create([
                        'name' => $recurrence->name,
                        'value' => $recurrence->value,
                        'maturity' => $currentDueDate->toDateString(),
                        'situation' => 'pending',
                        'category_id' => $recurrence->category_id,
                        'type' => $recurrence->type,
                        'note' => $recurrence->note,
                        'image' => null,
                        'user_id' => $recurrence->user_id,
                        'fixed' => true,
                        'repeat' => 0,
                        'recurrence_id' => $recurrence->id,
                    ]);
                });

                $hasGeneratedAny = true;
            }

            $newCount = $recurrence->occurrences_count + 1;
            $nextDate = $this->dateService->calculateDateForIndex(
                $recurrence->start_date,
                $recurrence->frequency,
                $newCount,
                $recurrence->interval,
                $recurrence->anchor_day
            );

            $newStatus = 'active';
            if ($recurrence->max_occurrences && $newCount >= $recurrence->max_occurrences) {
                $newStatus = 'completed';
            } elseif ($recurrence->end_date && $nextDate->gt($recurrence->end_date)) {
                $newStatus = 'completed';
            }

            $recurrence->update([
                'occurrences_count' => $newCount,
                'next_run_date' => $nextDate->toDateString(),
                'last_generated_at' => now(),
                'status' => $newStatus,
            ]);

            if ($newStatus === 'completed') {
                break;
            }
        }

        return $hasGeneratedAny;
    }

    /**
     * Cancela uma regra de recorrência sem remover os lançamentos existentes.
     */
    public function cancelRecurrence(Recurrence|int $recurrence): bool
    {
        $model = $recurrence instanceof Recurrence ? $recurrence : Recurrence::find($recurrence);
        if (!$model) {
            return false;
        }

        $model->update(['status' => 'canceled']);
        return true;
    }

    /**
     * Cancela a recorrência associada a uma conta.
     */
    public function cancelFromConta(Conta $conta): bool
    {
        if ($conta->recurrence_id) {
            $this->cancelRecurrence($conta->recurrence_id);
        }

        $conta->update([
            'fixed' => false,
            'recurrence_id' => null,
        ]);

        return true;
    }
}
