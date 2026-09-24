<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Conta;
use App\Models\InternalNotification;
use App\Models\User;
use App\Services\ReminderService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReminderTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'email' => 'user.reminder@test.com',
            'reminder_upcoming' => true,
            'reminder_days_before' => 3,
            'reminder_overdue' => true,
        ]);

        $this->category = Category::create([
            'name' => 'Geral',
            'user_id' => $this->user->id,
        ]);
    }

    public function test_generates_internal_notifications_correctly_for_due_today_upcoming_and_overdue(): void
    {
        $today = Carbon::today();

        // 1. Due today
        Conta::create([
            'name' => 'Luz Vencendo Hoje',
            'value' => 120.00,
            'maturity' => $today->format('Y-m-d'),
            'situation' => 'pending',
            'category_id' => $this->category->id,
            'user_id' => $this->user->id,
            'type' => 'saida',
        ]);

        // 2. Upcoming in 2 days
        Conta::create([
            'name' => 'Água Próxima',
            'value' => 80.00,
            'maturity' => (clone $today)->addDays(2)->format('Y-m-d'),
            'situation' => 'pending',
            'category_id' => $this->category->id,
            'user_id' => $this->user->id,
            'type' => 'saida',
        ]);

        // 3. Overdue 5 days ago
        Conta::create([
            'name' => 'Internet Vencida',
            'value' => 150.00,
            'maturity' => (clone $today)->subDays(5)->format('Y-m-d'),
            'situation' => 'pending',
            'category_id' => $this->category->id,
            'user_id' => $this->user->id,
            'type' => 'saida',
        ]);

        // 4. Paid account (should NOT generate notification)
        Conta::create([
            'name' => 'Gás Pago Hoje',
            'value' => 110.00,
            'maturity' => $today->format('Y-m-d'),
            'situation' => 'paid',
            'category_id' => $this->category->id,
            'user_id' => $this->user->id,
            'type' => 'saida',
        ]);

        $service = app(ReminderService::class);
        $service->syncRemindersForUser($this->user);

        $dueTodayNotif = InternalNotification::where('user_id', $this->user->id)->where('type', 'due_today')->first();
        $this->assertNotNull($dueTodayNotif);
        $this->assertEquals($today->format('Y-m-d'), $dueTodayNotif->reference_date->format('Y-m-d'));

        $upcomingNotif = InternalNotification::where('user_id', $this->user->id)->where('type', 'upcoming')->first();
        $this->assertNotNull($upcomingNotif);
        $this->assertEquals((clone $today)->addDays(2)->format('Y-m-d'), $upcomingNotif->reference_date->format('Y-m-d'));

        $overdueNotif = InternalNotification::where('user_id', $this->user->id)->where('type', 'overdue')->first();
        $this->assertNotNull($overdueNotif);
        $this->assertEquals((clone $today)->subDays(5)->format('Y-m-d'), $overdueNotif->reference_date->format('Y-m-d'));

        $this->assertEquals(3, InternalNotification::where('user_id', $this->user->id)->count());

        // Anti-duplication check: run sync again
        $service->syncRemindersForUser($this->user);
        $this->assertEquals(3, InternalNotification::where('user_id', $this->user->id)->count());
    }

    public function test_can_mark_notification_as_read_and_all_as_read(): void
    {
        $today = Carbon::today();

        $c = Conta::create([
            'name' => 'Conta Teste Lida',
            'value' => 50.00,
            'maturity' => $today->format('Y-m-d'),
            'situation' => 'pending',
            'category_id' => $this->category->id,
            'user_id' => $this->user->id,
            'type' => 'saida',
        ]);

        $service = app(ReminderService::class);
        $service->syncRemindersForUser($this->user);

        $notif = InternalNotification::where('user_id', $this->user->id)->firstOrFail();
        $this->assertNull($notif->read_at);

        // Mark single as read
        $response = $this->actingAs($this->user)->post(route('lembretes.read', $notif->id));
        $response->assertSessionHas('success');

        $notif->refresh();
        $this->assertNotNull($notif->read_at);

        // Mark all as read
        $responseAll = $this->actingAs($this->user)->post(route('lembretes.read-all'));
        $responseAll->assertSessionHas('success');
    }

    public function test_can_update_reminder_settings(): void
    {
        $response = $this->actingAs($this->user)->post(route('lembretes.settings'), [
            'reminder_upcoming' => 1,
            'reminder_days_before' => 7,
            'reminder_overdue' => 1,
        ]);

        $response->assertSessionHas('success');

        $this->user->refresh();
        $this->assertEquals(7, $this->user->reminder_days_before);
        $this->assertTrue($this->user->reminder_upcoming);
        $this->assertTrue($this->user->reminder_overdue);
    }
}
