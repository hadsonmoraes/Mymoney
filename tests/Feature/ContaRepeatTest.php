<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Conta;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContaRepeatTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->category = Category::create([
            'name' => 'Alimentação',
            'user_id' => $this->user->id,
        ]);
    }

    public function test_user_can_repeat_conta_multiple_times()
    {
        $conta = Conta::create([
            'name' => 'Academia',
            'value' => '100.00',
            'maturity' => '2026-09-10',
            'situation' => 'pending',
            'category_id' => $this->category->id,
            'type' => 'saida',
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->post(route('contas.repeat', ['id' => $conta->id]), [
            'count' => 5,
            'frequency' => 'monthly',
        ]);

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('success');

        // Conta original + 5 repetições = 6 contas
        $this->assertEquals(6, Conta::where('user_id', $this->user->id)->count());

        $conta->refresh();
        $this->assertNotNull($conta->repeat_group_id);
        $this->assertEquals(1, $conta->repeat_index);
        $this->assertEquals(6, $conta->repeat_total);

        // Verificar as 5 novas contas
        $repeticoes = Conta::where('parent_id', $conta->id)->orderBy('repeat_index')->get();
        $this->assertCount(5, $repeticoes);

        $expectedDates = [
            '2026-10-10',
            '2026-11-10',
            '2026-12-10',
            '2027-01-10',
            '2027-02-10',
        ];

        foreach ($repeticoes as $i => $rep) {
            $this->assertEquals($expectedDates[$i], $rep->maturity->toDateString());
            $this->assertEquals($conta->repeat_group_id, $rep->repeat_group_id);
            $this->assertEquals($i + 2, $rep->repeat_index);
            $this->assertEquals(6, $rep->repeat_total);
            $this->assertEquals('pending', $rep->situation);
            $this->assertEquals($conta->value, $rep->value);
        }
    }

    public function test_user_can_repeat_with_custom_interval()
    {
        $conta = Conta::create([
            'name' => 'Remédio',
            'value' => '50.00',
            'maturity' => '2026-09-01',
            'situation' => 'pending',
            'category_id' => $this->category->id,
            'type' => 'saida',
            'user_id' => $this->user->id,
        ]);

        $this->actingAs($this->user)->post(route('contas.repeat', ['id' => $conta->id]), [
            'count' => 3,
            'frequency' => 'custom',
            'interval' => 3,
        ]);

        $repeticoes = Conta::where('parent_id', $conta->id)->orderBy('maturity')->get();
        $this->assertCount(3, $repeticoes);
        $this->assertEquals('2026-09-04', $repeticoes[0]->maturity->toDateString());
        $this->assertEquals('2026-09-07', $repeticoes[1]->maturity->toDateString());
        $this->assertEquals('2026-09-10', $repeticoes[2]->maturity->toDateString());
    }

    public function test_user_cannot_repeat_conta_of_another_user()
    {
        $otherUser = User::factory()->create();
        $otherCategory = Category::create([
            'name' => 'Outra',
            'user_id' => $otherUser->id,
        ]);

        $contaOther = Conta::create([
            'name' => 'Conta Secreta',
            'value' => '999.00',
            'maturity' => '2026-09-10',
            'situation' => 'pending',
            'category_id' => $otherCategory->id,
            'type' => 'saida',
            'user_id' => $otherUser->id,
        ]);

        $response = $this->actingAs($this->user)->post(route('contas.repeat', ['id' => $contaOther->id]), [
            'count' => 3,
            'frequency' => 'monthly',
        ]);

        $response->assertNotFound();
        $this->assertEquals(0, Conta::where('parent_id', $contaOther->id)->count());
    }

    public function test_update_sequence_only_this()
    {
        $conta = Conta::create([
            'name' => 'Internet',
            'value' => '100.00',
            'maturity' => '2026-09-10',
            'situation' => 'pending',
            'category_id' => $this->category->id,
            'type' => 'saida',
            'user_id' => $this->user->id,
        ]);

        $this->actingAs($this->user)->post(route('contas.repeat', ['id' => $conta->id]), [
            'count' => 2,
            'frequency' => 'monthly',
        ]);

        // Editar somente a conta original
        $this->actingAs($this->user)->put(route('contas.update', ['id' => $conta->id]), [
            'name' => 'Internet Fibra Ultra',
            'value' => '120,00',
            'maturity' => '2026-09-10',
            'situation' => 'pending',
            'category_id' => $this->category->id,
            'type' => 'saida',
            'update_scope' => 'only_this',
        ]);

        $conta->refresh();
        $this->assertEquals('Internet Fibra Ultra', $conta->name);
        $this->assertEquals('120.00', $conta->value);

        // As repetições continuam com nome original 'Internet'
        $repeticoes = Conta::where('parent_id', $conta->id)->get();
        foreach ($repeticoes as $rep) {
            $this->assertEquals('Internet', $rep->name);
            $this->assertEquals('100.00', $rep->value);
        }
    }

    public function test_update_sequence_all()
    {
        $conta = Conta::create([
            'name' => 'Internet',
            'value' => '100.00',
            'maturity' => '2026-09-10',
            'situation' => 'pending',
            'category_id' => $this->category->id,
            'type' => 'saida',
            'user_id' => $this->user->id,
        ]);

        $this->actingAs($this->user)->post(route('contas.repeat', ['id' => $conta->id]), [
            'count' => 2,
            'frequency' => 'monthly',
        ]);
        $conta->refresh();

        // Editar toda a sequência
        $this->actingAs($this->user)->put(route('contas.update', ['id' => $conta->id]), [
            'name' => 'Internet Reajustada',
            'value' => '115,00',
            'maturity' => '2026-09-10',
            'situation' => 'pending',
            'category_id' => $this->category->id,
            'type' => 'saida',
            'update_scope' => 'all_sequence',
        ]);

        $all = Conta::where('repeat_group_id', $conta->repeat_group_id)->get();
        $this->assertCount(3, $all);
        foreach ($all as $item) {
            $this->assertEquals('Internet Reajustada', $item->name);
            $this->assertEquals('115.00', $item->value);
        }
    }

    public function test_delete_sequence_this_and_next()
    {
        $conta = Conta::create([
            'name' => 'Curso',
            'value' => '200.00',
            'maturity' => '2026-09-10',
            'situation' => 'pending',
            'category_id' => $this->category->id,
            'type' => 'saida',
            'user_id' => $this->user->id,
        ]);

        $this->actingAs($this->user)->post(route('contas.repeat', ['id' => $conta->id]), [
            'count' => 3,
            'frequency' => 'monthly',
        ]);

        // 4 contas no total: index 1, 2, 3, 4
        $reps = Conta::where('parent_id', $conta->id)->orderBy('repeat_index')->get();
        $segundaRep = $reps[0]; // index 2

        // Excluir a partir do index 2 (este e os próximos: 2, 3, 4)
        $this->actingAs($this->user)->delete(route('contas.destroy', ['id' => $segundaRep->id]), [
            'delete_scope' => 'this_and_next',
        ]);

        // Restou apenas a conta original (index 1)
        $restantes = Conta::where('user_id', $this->user->id)->get();
        $this->assertCount(1, $restantes);
        $this->assertEquals($conta->id, $restantes->first()->id);
    }

    public function test_transaction_rollback_when_repeat_fails()
    {
        $conta = Conta::create([
            'name' => 'Teste Falha',
            'value' => '100.00',
            'maturity' => '2026-09-10',
            'situation' => 'pending',
            'category_id' => $this->category->id,
            'type' => 'saida',
            'user_id' => $this->user->id,
        ]);

        $createdCount = 0;
        Conta::creating(function ($model) use (&$createdCount) {
            if ($model->name === 'Teste Falha' && $model->parent_id !== null) {
                $createdCount++;
                if ($createdCount === 2) {
                    throw new \RuntimeException('Falha simulada no meio da transação');
                }
            }
        });

        try {
            $service = app(\App\Services\ContaRepeatService::class);
            $service->repeat($conta, [
                'count' => 4,
                'frequency' => 'monthly',
            ]);
        } catch (\RuntimeException $e) {
            // Exceção esperada
        }

        // Nenhuma repetição parcial deve ter ficado no banco de dados
        $this->assertEquals(0, Conta::where('parent_id', $conta->id)->count());
        $conta->refresh();
        $this->assertEquals(1, $conta->repeat_total ?? 1);
    }
}
