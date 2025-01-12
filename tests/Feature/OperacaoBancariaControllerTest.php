<?php

namespace Tests\Feature;

use App\Models\SaldoConta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Conta;

# sail artisan test --filter=OperacaoBancariaControllerTest
class OperacaoBancariaControllerTest extends TestCase
{
    use RefreshDatabase;

    # sail artisan test --filter=OperacaoBancariaControllerTest::test_saldo_retorna_saldo_de_conta
    public function test_saldo_retorna_saldo_de_conta(): void
    {
        // Cria uma conta no banco de dados com saldo inicial
        $conta = Conta::factory()->create();
        SaldoConta::create([
            'contas_id' => $conta->id,
            'moeda' => 'USD',
            'valor' => 100.00,
        ]);

        // Faz a requisição para o endpoint saldo
        $response = $this->getJson(route('operacao.saldo', ['contas_id' => $conta->id]));

        // Verifica se o status da resposta é 200 (OK)
        $response->assertStatus(200);

        // Verifica se a resposta contém o saldo correto
        $response->assertJsonFragment([
            'contas_id' => $conta->id,
            'moeda' => 'USD',
            'valor' => '100.00',
        ]);
    }

    # sail artisan test --filter=OperacaoBancariaControllerTest::test_deposito_realiza_deposito_com_sucesso
    public function test_deposito_realiza_deposito_com_sucesso(): void
    {
        // Cria uma conta no banco de dados
        $conta = Conta::factory()->create();

        // Dados válidos para depósito
        $data = [
            'contas_id' => $conta->id,
            'valor' => 100.00,
            'moeda' => 'USD',
        ];

        // Faz a requisição para o endpoint deposito
        $response = $this->putJson(route('operacao.deposito'), $data);

        // Verifica se o status da resposta é 200
        $response->assertStatus(200);

        // Verifica se o depósito foi realizado no banco de dados
        $this->assertDatabaseHas('saldo_contas', [
            'contas_id' => $conta->id,
            'moeda' => 'USD',
            'valor' => 100.00,
        ]);
    }

    # sail artisan test --filter=OperacaoBancariaControllerTest::test_deposito_retorna_erro_com_dados_invalidos
    public function test_deposito_retorna_erro_com_dados_invalidos(): void
    {
        // Cria uma conta no banco de dados
        $conta = Conta::factory()->create();

        // Dados inválidos para depósito
        $data = [
            'contas_id' => $conta->id,
            'valor' => 100.00,
            'moeda' => 'AAA', // Moeda inválida
        ];

        // Faz a requisição para o endpoint deposito
        $response = $this->putJson(route('operacao.deposito'), $data);

        // Verifica se o status da resposta é 500
        $response->assertStatus(500);

        // Verifica se a resposta contém mensagens de erro
        $response->assertJsonFragment(['error' => 'Erro ao realizar a transação']);
    }

    # sail artisan test --filter=OperacaoBancariaControllerTest::test_saque_realiza_saque_com_sucesso
    public function test_saque_realiza_saque_com_sucesso(): void
    {
        // Cria uma conta no banco de dados com saldo inicial
        $conta = Conta::factory()->create();
        SaldoConta::create([
            'contas_id' => $conta->id,
            'moeda' => 'USD',
            'valor' => 100.00,
        ]);

        // Dados válidos para saque
        $data = [
            'contas_id' => $conta->id,
            'valor' => 50.00,
            'moeda' => 'USD',
        ];

        // Faz a requisição para o endpoint saque
        $response = $this->putJson(route('operacao.saque'), $data);

        // Verifica se o status da resposta é 200 (OK)
        $response->assertStatus(200);

        // Verifica se o saque foi realizado no banco de dados
        $this->assertDatabaseHas('saldo_contas', [
            'contas_id' => $conta->id,
            'moeda' => 'USD',
            'valor' => 50.00,
        ]);
    }

    # sail artisan test --filter=OperacaoBancariaControllerTest::test_saque_retorna_erro_se_saldo_insuficiente
    public function test_saque_retorna_erro_se_saldo_insuficiente(): void
    {
        // Cria uma conta no banco de dados com saldo inicial
        $conta = Conta::factory()->create();
        SaldoConta::create([
            'contas_id' => $conta->id,
            'moeda' => 'USD',
            'valor' => 50.00,
        ]);

        // Dados inválidos para saque (saldo insuficiente)
        $data = [
            'contas_id' => $conta->id,
            'valor' => 100.00,
            'moeda' => 'USD',
        ];

        // Faz a requisição para o endpoint saque
        $response = $this->putJson(route('operacao.saque'), $data);

        // Verifica se o status da resposta é 400 (Bad Request)
        $response->assertStatus(400);

        // Verifica se a resposta contém mensagem de erro
        $response->assertJsonFragment([
            'error' => 'Saldo insuficiente para realizar a transação',
        ]);
    }

    # sail artisan test --filter=OperacaoBancariaControllerTest::test_saque_retorna_erro_com_dados_invalidos
    public function test_saque_retorna_erro_com_dados_invalidos(): void
    {
        // Cria uma conta no banco de dados
        $conta = Conta::factory()->create();

        // Dados inválidos para saque
        $data = [
            'contas_id' => $conta->id,
            'valor' => 40.00,
            'moeda' => 'BBB', // Moeda inválida
        ];

        // Faz a requisição para o endpoint saque
        $response = $this->putJson(route('operacao.saque'), $data);

        // Verifica se o status da resposta é 500 (Internal Server Error)
        $response->assertStatus(500);

        // Verifica se a resposta contém mensagens de erro
        $response->assertJsonFragment(['error' => 'Erro ao realizar a transação']);
    }
}
