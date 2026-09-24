<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Conta;
use App\Models\Recurrence;
use App\Models\User;
use App\Services\RecurrenceService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecurrenceTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->category = Category::create([
            'name' => 'Moradia',
            'user_id' => $this->user->id,
        ]);
    }

    public function test_create_conta_with_monthly_recurrence_rule()
    {
        $response = $this->actingAs($this->user)->post(route('contas.store'), [
            'name' => 'Netflix',
            'value' => '59,90',
            'maturity' => '2026-09-10',
            'situation' => 'pending',
            'category_id' => $this->category->id,
            'type' => 'saida',
            'recurrence_type' => 'monthly',
        ]);

        $response->assertRedirect(route('home'));

        $this->assertDatabaseHas('contas', [
            'name' => 'Netflix',
            'value' => '59.90',
            'user_id' => $this->user->id,
        ]);

        $rec = Recurrence::where('name', 'Netflix')->where('user_id', $this->user->id)->first();
        $this->assertNotNull($rec);
        $this->assertEquals('59.90', $rec->value);
        $this->assertEquals('monthly', $rec->frequency);
        $this->assertEquals('active', $rec->status);
        $this->assertEquals('2026-10-10', $rec->next_run_date->toDateString());
    }

    public function test_process_recurrences_artisan_command_generates_due_contas()
    {
        $conta = Conta::create([
            'name' => 'Internet',
            'value' => '120.00',
            'maturity' => '2026-08-15',
            'situation' => 'paid',
            'category_id' => $this->category->id,
            'type' => 'saida',
            'user_id' => $this->user->id,
        ]);

        $service = app(RecurrenceService::class);
        $recurrence = $service->createFromConta($conta, [
            'frequency' => 'monthly',
            'start_date' => '2026-08-15',
        ]);

        $this->assertEquals('2026-09-15', $recurrence->next_run_date->toDateString());

        // Executar o comando simulando data de 2026-09-20
        $this->artisan('finance:process-recurrences', ['--date' => '2026-09-20'])
            ->assertSuccessful();

        // Deve ter gerado a conta de 2026-09-15
        $novaConta = Conta::where('recurrence_id', $recurrence->id)->where('id', '!=', $conta->id)->first();
        $this->assertNotNull($novaConta);
        $this->assertEquals('Internet', $novaConta->name);
        $this->assertEquals('2026-09-15', $novaConta->maturity->toDateString());
        $this->assertEquals('pending', $novaConta->situation);

        $recurrence->refresh();
        $this->assertEquals('2026-10-15', $recurrence->next_run_date->toDateString());
        $this->assertEquals(2, $recurrence->occurrences_count);

        // Prevenção de duplicidade: rodar novamente não deve duplicar o lançamento de 2026-09-15
        $this->artisan('finance:process-recurrences', ['--date' => '2026-09-20'])
            ->assertSuccessful();

        $this->assertEquals(1, Conta::where('recurrence_id', $recurrence->id)->whereDate('maturity', '2026-09-15')->count());
    }

    public function test_recurrence_stops_when_reaching_max_occurrences()
    {
        $conta = Conta::create([
            'name' => 'Parcela do Curso',
            'value' => '300.00',
            'maturity' => '2026-01-10',
            'situation' => 'paid',
            'category_id' => $this->category->id,
            'type' => 'saida',
            'user_id' => $this->user->id,
        ]);

        $service = app(RecurrenceService::class);
        $recurrence = $service->createFromConta($conta, [
            'frequency' => 'monthly',
            'start_date' => '2026-01-10',
            'max_occurrences' => 3, // Original (1) + mais 2 = 3
        ]);

        // Simula avanço de tempo para gerar as ocorrências restantes
        $this->artisan('finance:process-recurrences', ['--date' => '2026-03-15'])
            ->assertSuccessful();

        $recurrence->refresh();
        $this->assertEquals('completed', $recurrence->status);
        $this->assertEquals(3, $recurrence->occurrences_count);

        // Total de contas vinculadas = original (1) + 2 geradas = 3 contas
        $this->assertEquals(3, Conta::where('name', 'Parcela do Curso')->count());
    }

    public function test_cancel_recurrence_does_not_delete_existing_contas()
    {
        $conta = Conta::create([
            'name' => 'Academia',
            'value' => '90.00',
            'maturity' => '2026-08-01',
            'situation' => 'paid',
            'category_id' => $this->category->id,
            'type' => 'saida',
            'user_id' => $this->user->id,
        ]);

        $service = app(RecurrenceService::class);
        $recurrence = $service->createFromConta($conta, [
            'frequency' => 'monthly',
            'start_date' => '2026-08-01',
        ]);

        // Cancelar recorrência
        $response = $this->actingAs($this->user)->post(route('contas.cancelar-recorrencia', ['id' => $conta->id]));
        $response->assertRedirect();

        $recurrence->refresh();
        $this->assertEquals('canceled', $recurrence->status);

        // A conta original continua existindo no banco
        $this->assertDatabaseHas('contas', [
            'id' => $conta->id,
            'name' => 'Academia',
        ]);
    }
}
