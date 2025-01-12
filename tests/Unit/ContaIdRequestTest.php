<?php

namespace Tests\Unit;

use App\Http\Requests\ContaIdRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

# sail artisan test --filter=ContaIdRequestTest
class ContaIdRequestTest extends TestCase
{
    # sail artisan test --filter=ContaIdRequestTest::test_conta_id_obrigatorio_null_falha
    public function test_contas_id_obrigatorio_null_falha()
    {
        $request = new ContaIdRequest();
        $rules = $request->rules();

        $validator = Validator::make(
            ['contas_id' => null],
            ['contas_id' => $rules['contas_id']]
        );
        $this->assertFalse($validator->passes());
    }

    # sail artisan test --filter=ContaIdRequestTest::test_contas_id_obrigatorio_vazio_falha
    public function test_contas_id_obrigatorio_vazio_falha()
    {
        $request = new ContaIdRequest();
        $rules = $request->rules();

        $validator = Validator::make(
            ['contas_id' => ''],
            ['contas_id' => $rules['contas_id']]
        );
        $this->assertFalse($validator->passes());
    }

    # sail artisan test --filter=ContaIdRequestTest::test_contas_id_invalido_string_falha
    public function test_contas_id_invalido_string_falha()
    {
        $request = new ContaIdRequest();
        $rules = $request->rules();

        $validator = Validator::make(
            ['contas_id' => 'abc'],
            ['contas_id' => $rules['contas_id']]
        );
        $this->assertFalse($validator->passes());
    }

    # sail artisan test --filter=ContaIdRequestTest::test_contas_id_invalido_negativo_falha
    public function test_contas_id_invalido_negativo_falha()
    {
        $request = new ContaIdRequest();
        $rules = $request->rules();

        $validator = Validator::make(
            ['contas_id' => -1],
            ['contas_id' => $rules['contas_id']]
        );
        $this->assertFalse($validator->passes());
    }

    # sail artisan test --filter=ContaIdRequestTest::test_contas_id_valido_passa
    public function test_contas_id_valido_passa()
    {
        $request = new ContaIdRequest();
        $rules = $request->rules();

        $validator = Validator::make(
            ['contas_id' => 1],
            ['contas_id' => $rules['contas_id']]
        );
        $this->assertTrue($validator->passes());
    }
}
