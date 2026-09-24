<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Conta;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ExcelImportTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'email' => 'user.excel@test.com',
        ]);

        $this->category = Category::create([
            'name' => 'Alimentação',
            'user_id' => $this->user->id,
        ]);
    }

    public function test_can_download_import_template_csv(): void
    {
        $response = $this->actingAs($this->user)->get(route('contas.importar.modelo'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('Data;Descrição;Tipo;Valor;Categoria;Situação', $response->getContent());
    }

    public function test_can_preview_valid_csv_file(): void
    {
        $csvContent = "Nome;Vencimento;Valor;Tipo;Categoria;Situação;Nota\n";
        $csvContent .= "Supermercado;25/09/2026;R$ 350,50;Saída;Alimentação;Pendente;Compras do mês\n";
        $csvContent .= "Salário;05/10/2026;5.000,00;Entrada;Salário;Pago;Mensal\n";

        $file = UploadedFile::fake()->createWithContent('planilha.csv', $csvContent);

        $response = $this->actingAs($this->user)->postJson(route('contas.importar.preview'), [
            'import_file' => $file,
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('data.total_rows', 2);
        $response->assertJsonPath('data.valid_rows', 2);
        $response->assertJsonPath('data.errors_count', 0);
    }

    public function test_can_confirm_import_and_create_contas_and_categories(): void
    {
        $csvContent = "Nome;Vencimento;Valor;Tipo;Categoria;Situação;Nota\n";
        $csvContent .= "Supermercado;25/09/2026;R$ 350,50;Saída;Alimentação;Pendente;Compras\n";
        $csvContent .= "Consultoria Nova;10/10/2026;1.500,00;Entrada;Serviços TI;Pago;Freelance\n";

        $file = UploadedFile::fake()->createWithContent('planilha.csv', $csvContent);

        // Preview sets the session and returns token
        $previewResponse = $this->actingAs($this->user)->postJson(route('contas.importar.preview'), [
            'import_file' => $file,
        ]);
        $token = $previewResponse->json('data.import_token');

        // Confirm
        $response = $this->actingAs($this->user)->post(route('contas.importar.confirm'), [
            'import_token' => $token,
            'auto_create_categories' => 1,
        ]);

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('success');

        // Check contas created
        $this->assertDatabaseHas('contas', [
            'name' => 'Supermercado',
            'user_id' => $this->user->id,
            'type' => 'saida',
            'situation' => 'pending',
        ]);

        $this->assertDatabaseHas('contas', [
            'name' => 'Consultoria Nova',
            'user_id' => $this->user->id,
            'type' => 'entrada',
            'situation' => 'paid',
        ]);

        // Check new category was auto created
        $this->assertDatabaseHas('category', [
            'name' => 'Serviços TI',
            'user_id' => $this->user->id,
        ]);
    }
}
