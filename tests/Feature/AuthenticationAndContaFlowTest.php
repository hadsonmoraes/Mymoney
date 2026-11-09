<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Conta;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

class AuthenticationAndContaFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_log_in_and_create_a_conta(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'https://api.pwnedpasswords.com/range/*' => Http::response('', 200),
        ]);

        $this->get(route('login'))
            ->assertOk()
            ->assertDontSee('sidebar-expanded')
            ->assertSee('Entrar na sua conta')
            ->assertSee(route('register'));

        $this->get(route('register'))
            ->assertOk()
            ->assertDontSee('sidebar-expanded')
            ->assertSee('password_confirmation');

        $this->get(route('password.request'))
            ->assertOk()
            ->assertDontSee('sidebar-expanded')
            ->assertSee('Digite o endereço de e-mail da conta.');

        $this->get(route('password.reset', ['token' => 'layout-test-token']))
            ->assertOk()
            ->assertDontSee('sidebar-expanded')
            ->assertSee('Confirme seu e-mail e crie a nova senha.');

        $email = 'flow-'.Str::uuid().'@example.test';
        $password = 'FlowTest!9_'.Str::random(16);

        $this->post(route('register'), [
            'name' => 'Conta de Teste',
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $password,
        ])->assertRedirect('/home');

        $this->assertAuthenticated();

        $user = User::query()->where('email', $email)->firstOrFail();
        $this->assertSame(16, Category::query()->where('user_id', $user->id)->count());

        $this->post(route('logout'))->assertRedirect('/');
        $this->assertGuest();

        $this->post(route('login'), [
            'email' => $email,
            'password' => $password,
        ])->assertRedirect('/home');

        $this->assertAuthenticatedAs($user);

        $this->get(route('contas.create'))
            ->assertOk()
            ->assertSee('Criar conta');

        $category = Category::query()
            ->where('user_id', $user->id)
            ->where('name', 'Outros')
            ->firstOrFail();
        $maturity = now()->toDateString();

        $this->post(route('contas.store'), [
            'name' => 'Conta do fluxo de teste',
            'value' => '79,90',
            'maturity' => $maturity,
            'situation' => 'pending',
            'category_id' => $category->id,
            'type' => 'saida',
            'recurrence_type' => 'none',
        ])->assertRedirect(route('home'));

        $this->assertDatabaseHas('contas', [
            'name' => 'Conta do fluxo de teste',
            'value' => '79.90',
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);

        $conta = Conta::query()->where('name', 'Conta do fluxo de teste')->firstOrFail();
        $this->assertSame($maturity, $conta->maturity->toDateString());

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Conta do fluxo de teste');
    }
}
