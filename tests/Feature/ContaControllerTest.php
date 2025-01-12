<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Conta;

# sail artisan test --filter=ContaControllerTest
class ContaControllerTest extends TestCase
{
    use RefreshDatabase;
    # sail artisan test --filter=ContaControllerTest::test_index_retorna_lista_de_contas
    public function test_index_retorna_lista_de_contas(): void
    {
        // Cria contas no banco de dados
        Conta::factory()->count(3)->create();

        // Faz a requisição para o endpoint index
        $response = $this->getJson(route('conta.index'));

        // Verifica se o status da resposta é 200 (OK)
        $response->assertStatus(200);

        // Verifica se a resposta contém exatamente 3 contas
        $response->assertJsonCount(3);
    }

    # sail artisan test --filter=ContaControllerTest::test_store_cria_uma_nova_conta_com_dados_validos
    public function test_store_cria_uma_nova_conta_com_dados_validos(): void
    {
        // Dados válidos
        $data = [
            'nome_titular' => 'João Silva',
            'cpf' => '12345678901',
        ];

        // Faz a requisição para o endpoint store
        $response = $this->postJson(route('conta.store'), $data);

        // Verifica se o status da resposta é 201 (Created)
        $response->assertStatus(201);

        // Verifica se a conta foi salva no banco de dados
        $this->assertDatabaseHas('contas', $data);

        // Verifica se a resposta contém os dados corretos
        $response->assertJsonFragment($data);
    }

    # sail artisan test --filter=ContaControllerTest::test_store_retorna_erro_com_dados_invalidos
    public function test_store_retorna_erro_com_dados_invalidos(): void
    {
        // Dados inválidos
        $data = [
            'nome_titular' => '', // Nome vazio
            'cpf' => '123', // CPF inválido
        ];

        // Faz a requisição para o endpoint store
        $response = $this->postJson(route('conta.store'), $data);

        // Verifica se o status da resposta é 422 (Unprocessable Entity)
        $response->assertStatus(422);

        // Verifica se a resposta contém mensagens de erro
        $response->assertJsonValidationErrors(['nome_titular', 'cpf']);
    }
}
