<?php

namespace App\Services;

use Carbon\Carbon;
use InvalidArgumentException;

class DateRecurrenceService
{
    /**
     * Calcula uma data de ocorrência com base na data de início, frequência, índice e dia âncora.
     *
     * @param Carbon|string $startDate
     * @param string $frequency weekly, biweekly, monthly, yearly, custom
     * @param int $occurrenceIndex Índice da ocorrência (0 = data inicial se incluída, 1 = 1ª repetição, 2 = 2ª...)
     * @param int $interval Intervalo (ex: a cada X dias ou a cada N meses)
     * @param int|null $anchorDay Dia do mês original (1 a 31)
     * @return Carbon
     */
    public function calculateDateForIndex(
        Carbon|string $startDate,
        string $frequency,
        int $occurrenceIndex,
        int $interval = 1,
        ?int $anchorDay = null
    ): Carbon {
        $base = $startDate instanceof Carbon ? $startDate->copy() : Carbon::parse($startDate);
        $anchor = $anchorDay ?? $base->day;
        $interval = max(1, $interval);

        if ($occurrenceIndex === 0) {
            return $base->copy();
        }

        return match ($frequency) {
            'weekly' => $base->copy()->addWeeks($occurrenceIndex * $interval),
            'biweekly' => $base->copy()->addDays($occurrenceIndex * 15 * $interval),
            'monthly' => $this->calculateMonthlyDate($base, $occurrenceIndex * $interval, $anchor),
            'yearly' => $this->calculateYearlyDate($base, $occurrenceIndex * $interval, $anchor),
            'custom' => $base->copy()->addDays($occurrenceIndex * $interval),
            default => throw new InvalidArgumentException("Frequência inválida: {$frequency}"),
        };
    }

    /**
     * Calcula a próxima data a partir de uma data atual de referência.
     */
    public function getNextDate(
        Carbon|string $currentDate,
        string $frequency,
        int $interval = 1,
        ?int $anchorDay = null
    ): Carbon {
        return $this->calculateDateForIndex($currentDate, $frequency, 1, $interval, $anchorDay);
    }

    /**
     * Gera um array de datas sequenciais para N ocorrências.
     *
     * @param Carbon|string $startDate
     * @param string $frequency
     * @param int $count Quantidade de repetições a gerar
     * @param int $interval
     * @param int|null $anchorDay
     * @param bool $includeStart Se true, o 1º elemento é a própria startDate (totalizando $count ocorrências)
     * @return Carbon[]
     */
    public function generateSequence(
        Carbon|string $startDate,
        string $frequency,
        int $count,
        int $interval = 1,
        ?int $anchorDay = null,
        bool $includeStart = false
    ): array {
        if ($count <= 0) {
            return [];
        }

        $dates = [];
        $startIndex = $includeStart ? 0 : 1;
        $endIndex = $includeStart ? ($count - 1) : $count;

        for ($i = $startIndex; $i <= $endIndex; $i++) {
            $dates[] = $this->calculateDateForIndex($startDate, $frequency, $i, $interval, $anchorDay);
        }

        return $dates;
    }

    /**
     * Calcula a data mensal preservando o dia âncora (ex: 31/01 -> 28/02 -> 31/03 -> 30/04).
     */
    protected function calculateMonthlyDate(Carbon $baseDate, int $monthsToAdd, int $anchorDay): Carbon
    {
        $target = $baseDate->copy()->startOfMonth()->addMonthsNoOverflow($monthsToAdd);
        $daysInTargetMonth = $target->daysInMonth;
        $day = min($anchorDay, $daysInTargetMonth);

        return $target->copy()->setDay($day);
    }

    /**
     * Calcula a data anual preservando dia e mês de origem (respeitando anos bissextos).
     */
    protected function calculateYearlyDate(Carbon $baseDate, int $yearsToAdd, int $anchorDay): Carbon
    {
        $targetYear = $baseDate->year + $yearsToAdd;
        $targetMonth = $baseDate->month;
        $daysInTargetMonth = Carbon::create($targetYear, $targetMonth, 1)->daysInMonth;
        $day = min($anchorDay, $daysInTargetMonth);

        return Carbon::create($targetYear, $targetMonth, $day, 0, 0, 0);
    }
}
