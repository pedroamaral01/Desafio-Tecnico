<?php

namespace Tests\Unit;

use App\Http\Requests\MoedaRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

# sail artisan test --filter=MoedaRequestTest
class MoedaRequestTest extends TestCase
{
    # sail artisan test --filter=MoedaRequestTest::test_moeda_null_passa
    public function test_moeda_null_passa()
    {
        $request = new MoedaRequest();
        $rules = $request->rules();

        $validator = Validator::make(
            ['moeda' => null],
            ['moeda' => $rules['moeda']]
        );
        $this->assertTrue($validator->passes());
    }

    # sail artisan test --filter=MoedaRequestTest::test_moeda_vazio_passa
    public function test_moeda_vazio_passa()
    {
        $request = new MoedaRequest();
        $rules = $request->rules();

        $validator = Validator::make(
            ['moeda' => ''],
            ['moeda' => $rules['moeda']]
        );
        $this->assertTrue($validator->passes());
    }

    # sail artisan test --filter=MoedaRequestTest::test_moeda_valido_passa
    public function test_moeda_valido_passa()
    {
        $request = new MoedaRequest();
        $rules = $request->rules();

        $validator = Validator::make(
            ['moeda' => 'USD'],
            ['moeda' => $rules['moeda']]
        );
        $this->assertTrue($validator->passes());
    }

    # sail artisan test --filter=MoedaRequestTest::test_moeda_invalido_falha
    public function test_moeda_invalido_falha()
    {
        $request = new MoedaRequest();
        $rules = $request->rules();

        $validator = Validator::make(
            ['moeda' => 'INVALID'],
            ['moeda' => $rules['moeda']]
        );
        $this->assertFalse($validator->passes());
    }

    # sail artisan test --filter=MoedaRequestTest::test_moeda_minusculas_falha
    public function test_moeda_minusculas_falha()
    {
        $request = new MoedaRequest();
        $rules = $request->rules();

        $validator = Validator::make(
            ['moeda' => 'usd'],
            ['moeda' => $rules['moeda']]
        );
        $this->assertFalse($validator->passes());
    }

    # sail artisan test --filter=MoedaRequestTest::test_moeda_acima_de_3_falha
    public function test_moeda_acima_de_3_falha()
    {
        $request = new MoedaRequest();
        $rules = $request->rules();

        $validator = Validator::make(
            ['moeda' => 'USDA'],
            ['moeda' => $rules['moeda']]
        );
        $this->assertFalse($validator->passes());
    }

    # sail artisan test --filter=MoedaRequestTest::test_moeda_abaixo_de_3_falha
    public function test_moeda_abaixo_de_3_falha()
    {
        $request = new MoedaRequest();
        $rules = $request->rules();

        $validator = Validator::make(
            ['moeda' => 'US'],
            ['moeda' => $rules['moeda']]
        );
        $this->assertFalse($validator->passes());
    }

    # sail artisan test --filter=MoedaRequestTest::test_moeda_3_numeros_falha
    public function test_moeda_3_numeros_falha()
    {
        $request = new MoedaRequest();
        $rules = $request->rules();

        $validator = Validator::make(
            ['moeda' => '123'],
            ['moeda' => $rules['moeda']]
        );
        $this->assertFalse($validator->passes());
    }
}
