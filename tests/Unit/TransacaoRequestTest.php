<?php

namespace Tests\Unit;

use App\Http\Requests\TransacaoRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

# sail artisan test --filter=TransacaoRequestTest
class TransacaoRequestTest extends TestCase
{
    # sail artisan test --filter=TransacaoRequestTest::test_contas_id_obrigatorio_null_falha
    public function test_contas_id_obrigatorio_null_falha()
    {
        $request = new TransacaoRequest();
        $rules = $request->rules();

        $validator = Validator::make(
            ['contas_id' => null],
            ['contas_id' => $rules['contas_id']]
        );
        $this->assertFalse($validator->passes());
    }

    # sail artisan test --filter=TransacaoRequestTest::test_contas_id_obrigatorio_vazio_falha
    public function test_contas_id_obrigatorio_vazio_falha()
    {
        $request = new TransacaoRequest();
        $rules = $request->rules();

        $validator = Validator::make(
            ['contas_id' => ''],
            ['contas_id' => $rules['contas_id']]
        );
        $this->assertFalse($validator->passes());
    }

    # sail artisan test --filter=TransacaoRequestTest::test_contas_id_invalido_string_falha
    public function test_contas_id_invalido_string_falha()
    {
        $request = new TransacaoRequest();
        $rules = $request->rules();

        $validator = Validator::make(
            ['contas_id' => 'abc'],
            ['contas_id' => $rules['contas_id']]
        );
        $this->assertFalse($validator->passes());
    }

    # sail artisan test --filter=TransacaoRequestTest::test_contas_id_invalido_negativo_falha
    public function test_contas_id_invalido_negativo_falha()
    {
        $request = new TransacaoRequest();
        $rules = $request->rules();

        $validator = Validator::make(
            ['contas_id' => -1],
            ['contas_id' => $rules['contas_id']]
        );
        $this->assertFalse($validator->passes());
    }

    # sail artisan test --filter=TransacaoRequestTest::test_contas_id_valido_passa
    public function test_contas_id_valido_passa()
    {
        $request = new TransacaoRequest();
        $rules = $request->rules();

        $validator = Validator::make(
            ['contas_id' => 1],
            ['contas_id' => $rules['contas_id']]
        );
        $this->assertTrue($validator->passes());
    }

    # sail artisan test --filter=TransacaoRequestTest::test_valor_obrigatorio_null_falha
    public function test_valor_obrigatorio_null_falha()
    {
        $request = new TransacaoRequest();
        $rules = $request->rules();

        $validator = Validator::make(
            ['valor' => null],
            ['valor' => $rules['valor']]
        );
        $this->assertFalse($validator->passes());
    }

    # sail artisan test --filter=TransacaoRequestTest::test_valor_obrigatorio_vazio_falha
    public function test_valor_obrigatorio_vazio_falha()
    {
        $request = new TransacaoRequest();
        $rules = $request->rules();

        $validator = Validator::make(
            ['valor' => ''],
            ['valor' => $rules['valor']]
        );
        $this->assertFalse($validator->passes());
    }

    # sail artisan test --filter=TransacaoRequestTest::test_valor_invalido_string_falha
    public function test_valor_invalido_string_falha()
    {
        $request = new TransacaoRequest();
        $rules = $request->rules();

        $validator = Validator::make(
            ['valor' => 'abc'],
            ['valor' => $rules['valor']]
        );
        $this->assertFalse($validator->passes());
    }

    # sail artisan test --filter=TransacaoRequestTest::test_valor_invalido_negativo_falha
    public function test_valor_invalido_negativo_falha()
    {
        $request = new TransacaoRequest();
        $rules = $request->rules();

        $validator = Validator::make(
            ['valor' => -1],
            ['valor' => $rules['valor']]
        );
        $this->assertFalse($validator->passes());
    }

    # sail artisan test --filter=TransacaoRequestTest::test_valor_formato_incorreto_falha
    public function test_valor_formato_incorreto_falha()
    {
        $request = new TransacaoRequest();
        $rules = $request->rules();

        $validator = Validator::make(
            ['valor' => '1000.123'],
            ['valor' => $rules['valor']]
        );
        $this->assertFalse($validator->passes());
    }

    # sail artisan test --filter=TransacaoRequestTest::test_valor_menor_que_minimo_falha
    public function test_valor_menor_que_minimo_falha()
    {
        $request = new TransacaoRequest();
        $rules = $request->rules();

        $validator = Validator::make(
            ['valor' => '0.001'],
            ['valor' => $rules['valor']]
        );
        $this->assertFalse($validator->passes());
    }

    # sail artisan test --filter=TransacaoRequestTest::test_valor_valido_passa
    public function test_valor_valido_passa()
    {
        $request = new TransacaoRequest();
        $rules = $request->rules();

        $validator = Validator::make(
            ['valor' => '100.50'],
            ['valor' => $rules['valor']]
        );
        $this->assertTrue($validator->passes());
    }

    # sail artisan test --filter=TransacaoRequestTest::test_moeda_obrigatorio_null_falha
    public function test_moeda_obrigatorio_null_falha()
    {
        $request = new TransacaoRequest();
        $rules = $request->rules();

        $validator = Validator::make(
            ['moeda' => null],
            ['moeda' => $rules['moeda']]
        );
        $this->assertFalse($validator->passes());
    }

    # sail artisan test --filter=TransacaoRequestTest::test_moeda_obrigatorio_vazio_falha
    public function test_moeda_obrigatorio_vazio_falha()
    {
        $request = new TransacaoRequest();
        $rules = $request->rules();

        $validator = Validator::make(
            ['moeda' => ''],
            ['moeda' => $rules['moeda']]
        );
        $this->assertFalse($validator->passes());
    }

    # sail artisan test --filter=TransacaoRequestTest::test_moeda_valido_passa
    public function test_moeda_valido_passa()
    {
        $request = new TransacaoRequest();
        $rules = $request->rules();

        $validator = Validator::make(
            ['moeda' => 'USD'],
            ['moeda' => $rules['moeda']]
        );
        $this->assertTrue($validator->passes());
    }

    # sail artisan test --filter=TransacaoRequestTest::test_moeda_invalido_falha
    public function test_moeda_invalido_falha()
    {
        $request = new TransacaoRequest();
        $rules = $request->rules();

        $validator = Validator::make(
            ['moeda' => 'INVALID'],
            ['moeda' => $rules['moeda']]
        );
        $this->assertFalse($validator->passes());
    }

    # sail artisan test --filter=TransacaoRequestTest::test_moeda_minusculas_falha
    public function test_moeda_minusculas_falha()
    {
        $request = new TransacaoRequest();
        $rules = $request->rules();

        $validator = Validator::make(
            ['moeda' => 'usd'],
            ['moeda' => $rules['moeda']]
        );
        $this->assertFalse($validator->passes());
    }

    # sail artisan test --filter=TransacaoRequestTest::test_moeda_acima_de_3_falha
    public function test_moeda_acima_de_3_falha()
    {
        $request = new TransacaoRequest();
        $rules = $request->rules();

        $validator = Validator::make(
            ['moeda' => 'USDA'],
            ['moeda' => $rules['moeda']]
        );
        $this->assertFalse($validator->passes());
    }

    # sail artisan test --filter=TransacaoRequestTest::test_moeda_abaixo_de_3_falha
    public function test_moeda_abaixo_de_3_falha()
    {
        $request = new TransacaoRequest();
        $rules = $request->rules();

        $validator = Validator::make(
            ['moeda' => 'US'],
            ['moeda' => $rules['moeda']]
        );
        $this->assertFalse($validator->passes());
    }

    # sail artisan test --filter=TransacaoRequestTest::test_moeda_3_numeros_falha
    public function test_moeda_3_numeros_falha()
    {
        $request = new TransacaoRequest();
        $rules = $request->rules();

        $validator = Validator::make(
            ['moeda' => '123'],
            ['moeda' => $rules['moeda']]
        );
        $this->assertFalse($validator->passes());
    }
}
