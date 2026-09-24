<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Conta;
use App\Models\User;
use App\Services\InstallmentService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InstallmentTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'email' => 'user.installment@test.com',
        ]);

        $this->category = Category::create([
            'name' => 'Cartão de Crédito',
            'user_id' => $this->user->id,
        ]);
    }

    public function test_can_manually_configure_legacy_conta_as_structured_installment(): void
    {
        $conta = Conta::create([
            'name' => 'Cartão - 3/12',
            'value' => 150.00,
            'maturity' => Carbon::today()->addMonths(2)->format('Y-m-d'),
            'situation' => 'pending',
            'category_id' => $this->category->id,
            'user_id' => $this->user->id,
            'type' => 'saida',
        ]);

        $this->assertFalse($conta->is_installment);

        $response = $this->actingAs($this->user)->post(route('contas.configurar-parcelamento', $conta->id), [
            'is_installment' => 1,
            'installment_number' => 3,
            'installments_total' => 12,
            'link_related' => 0,
        ]);

        $response->assertSessionHas('success');

        $conta->refresh();
        $this->assertTrue($conta->is_installment);
        $this->assertEquals(3, $conta->installment_number);
        $this->assertEquals(12, $conta->installments_total);
        $this->assertEquals('3/12', $conta->installment_label);
        $this->assertNotNull($conta->installment_group_id);
    }

    public function test_can_link_related_legacy_installments_automatically(): void
    {
        $c1 = Conta::create([
            'name' => 'Notebook Dell - 1/3',
            'value' => 1000.00,
            'maturity' => Carbon::today()->format('Y-m-d'),
            'situation' => 'paid',
            'category_id' => $this->category->id,
            'user_id' => $this->user->id,
            'type' => 'saida',
        ]);

        $c2 = Conta::create([
            'name' => 'Notebook Dell - 2/3',
            'value' => 1000.00,
            'maturity' => Carbon::today()->addMonth()->format('Y-m-d'),
            'situation' => 'pending',
            'category_id' => $this->category->id,
            'user_id' => $this->user->id,
            'type' => 'saida',
        ]);

        $c3 = Conta::create([
            'name' => 'Notebook Dell - 3/3',
            'value' => 1000.00,
            'maturity' => Carbon::today()->addMonths(2)->format('Y-m-d'),
            'situation' => 'pending',
            'category_id' => $this->category->id,
            'user_id' => $this->user->id,
            'type' => 'saida',
        ]);

        $response = $this->actingAs($this->user)->post(route('contas.configurar-parcelamento', $c1->id), [
            'is_installment' => 1,
            'installment_number' => 1,
            'installments_total' => 3,
            'link_related' => 1,
        ]);

        $response->assertSessionHas('success');

        $c1->refresh();
        $c2->refresh();
        $c3->refresh();

        $this->assertNotNull($c1->installment_group_id);
        $this->assertEquals($c1->installment_group_id, $c2->installment_group_id);
        $this->assertEquals($c1->installment_group_id, $c3->installment_group_id);

        $this->assertEquals(1, $c1->installment_number);
        $this->assertEquals(2, $c2->installment_number);
        $this->assertEquals(3, $c3->installment_number);
    }

    public function test_installment_summary_calculates_real_values_and_progress(): void
    {
        $groupId = 'group-summary-test';

        Conta::create([
            'name' => 'Smart TV 1/3',
            'value' => 500.00,
            'maturity' => Carbon::today()->subMonth()->format('Y-m-d'),
            'situation' => 'paid',
            'category_id' => $this->category->id,
            'user_id' => $this->user->id,
            'type' => 'saida',
            'installment_group_id' => $groupId,
            'installment_number' => 1,
            'installments_total' => 3,
        ]);

        Conta::create([
            'name' => 'Smart TV 2/3',
            'value' => 500.00,
            'maturity' => Carbon::today()->addMonth()->format('Y-m-d'),
            'situation' => 'pending',
            'category_id' => $this->category->id,
            'user_id' => $this->user->id,
            'type' => 'saida',
            'installment_group_id' => $groupId,
            'installment_number' => 2,
            'installments_total' => 3,
        ]);

        Conta::create([
            'name' => 'Smart TV 3/3',
            'value' => 500.00,
            'maturity' => Carbon::today()->addMonths(2)->format('Y-m-d'),
            'situation' => 'pending',
            'category_id' => $this->category->id,
            'user_id' => $this->user->id,
            'type' => 'saida',
            'installment_group_id' => $groupId,
            'installment_number' => 3,
            'installments_total' => 3,
        ]);

        $service = app(InstallmentService::class);
        $summary = $service->getInstallmentSummary($groupId, $this->user->id);

        $this->assertNotNull($summary);
        $this->assertEquals(3, $summary['total_installments']);
        $this->assertEquals(1, $summary['paid_count']);
        $this->assertEquals(2, $summary['remaining_count']);
        $this->assertEquals(1500.00, $summary['total_value']);
        $this->assertEquals(500.00, $summary['paid_value']);
        $this->assertEquals(1000.00, $summary['remaining_value']);
        $this->assertEquals(33, $summary['progress_percent']);
    }

    public function test_can_remove_installment_configuration(): void
    {
        $conta = Conta::create([
            'name' => 'Conta Parcelada',
            'value' => 100.00,
            'maturity' => Carbon::today()->format('Y-m-d'),
            'situation' => 'pending',
            'category_id' => $this->category->id,
            'user_id' => $this->user->id,
            'type' => 'saida',
            'installment_group_id' => 'to-remove',
            'installment_number' => 1,
            'installments_total' => 5,
        ]);

        $this->assertTrue($conta->is_installment);

        $response = $this->actingAs($this->user)->post(route('contas.configurar-parcelamento', $conta->id), [
            'is_installment' => 0,
        ]);

        $response->assertSessionHas('success');

        $conta->refresh();
        $this->assertFalse($conta->is_installment);
        $this->assertNull($conta->installment_group_id);
        $this->assertNull($conta->installment_number);
        $this->assertNull($conta->installments_total);
    }
}
