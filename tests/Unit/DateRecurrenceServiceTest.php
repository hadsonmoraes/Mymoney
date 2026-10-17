<?php

namespace Tests\Unit;

use App\Services\DateRecurrenceService;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class DateRecurrenceServiceTest extends TestCase
{
    private DateRecurrenceService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DateRecurrenceService();
    }

    public function test_preserves_anchor_day_across_different_month_lengths()
    {
        $startDate = Carbon::create(2025, 1, 31);
        $dates = $this->service->generateSequence($startDate, 'monthly', 5, 1, 31, true);

        $this->assertCount(5, $dates);
        // Janeiro: 31/01
        $this->assertEquals('2025-01-31', $dates[0]->toDateString());
        // Fevereiro (não bissexto): 28/02
        $this->assertEquals('2025-02-28', $dates[1]->toDateString());
        // Março: 31/03 (volta para 31!)
        $this->assertEquals('2025-03-31', $dates[2]->toDateString());
        // Abril: 30/04
        $this->assertEquals('2025-04-30', $dates[3]->toDateString());
        // Maio: 31/05 (volta para 31!)
        $this->assertEquals('2025-05-31', $dates[4]->toDateString());
    }

    public function test_handles_leap_year_february_correctly()
    {
        // 2024 é ano bissexto
        $startDate = Carbon::create(2024, 1, 31);
        $next = $this->service->calculateDateForIndex($startDate, 'monthly', 1, 1, 31);
        $this->assertEquals('2024-02-29', $next->toDateString());

        // 2025 não é ano bissexto
        $startDate2025 = Carbon::create(2025, 1, 31);
        $next2025 = $this->service->calculateDateForIndex($startDate2025, 'monthly', 1, 1, 31);
        $this->assertEquals('2025-02-28', $next2025->toDateString());
    }

    public function test_handles_weekly_frequency()
    {
        $startDate = Carbon::create(2026, 9, 7); // segunda-feira
        $dates = $this->service->generateSequence($startDate, 'weekly', 3, 1, null, false);

        $this->assertCount(3, $dates);
        $this->assertEquals('2026-09-14', $dates[0]->toDateString());
        $this->assertEquals('2026-09-21', $dates[1]->toDateString());
        $this->assertEquals('2026-09-28', $dates[2]->toDateString());
    }

    public function test_handles_biweekly_frequency_every_15_days()
    {
        $startDate = Carbon::create(2026, 9, 1);
        $dates = $this->service->generateSequence($startDate, 'biweekly', 2, 1, null, false);

        $this->assertCount(2, $dates);
        $this->assertEquals('2026-09-16', $dates[0]->toDateString());
        $this->assertEquals('2026-10-01', $dates[1]->toDateString());
    }

    public function test_handles_yearly_frequency_and_leap_year()
    {
        // 29 de fevereiro de 2024
        $startDate = Carbon::create(2024, 2, 29);
        $year1 = $this->service->calculateDateForIndex($startDate, 'yearly', 1, 1, 29);
        $year4 = $this->service->calculateDateForIndex($startDate, 'yearly', 4, 1, 29);

        // 2025 não é bissexto -> 28/02
        $this->assertEquals('2025-02-28', $year1->toDateString());
        // 2028 é bissexto -> 29/02 novamente
        $this->assertEquals('2028-02-29', $year4->toDateString());
    }

    public function test_handles_custom_interval_in_days()
    {
        $startDate = Carbon::create(2026, 9, 10);
        $dates = $this->service->generateSequence($startDate, 'custom', 3, 3, null, false);

        $this->assertCount(3, $dates);
        $this->assertEquals('2026-09-13', $dates[0]->toDateString());
        $this->assertEquals('2026-09-16', $dates[1]->toDateString());
        $this->assertEquals('2026-09-19', $dates[2]->toDateString());
    }

    public function test_handles_year_turnover()
    {
        $startDate = Carbon::create(2026, 11, 15);
        $dates = $this->service->generateSequence($startDate, 'monthly', 3, 1, 15, false);

        $this->assertEquals('2026-12-15', $dates[0]->toDateString());
        $this->assertEquals('2027-01-15', $dates[1]->toDateString());
        $this->assertEquals('2027-02-15', $dates[2]->toDateString());
    }
}
