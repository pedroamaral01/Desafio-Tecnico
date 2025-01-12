<?php

namespace Tests\Unit;

use App\Http\Requests\CriaContaRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

# sail artisan test --filter=CriaContaRequestTest
class CriaContaRequestTest extends TestCase
{
    # sail artisan test --filter=CriaContaRequestTest::test_nome_titular_obrigatorio_null_falha
    public function test_nome_titular_obrigatorio_null_falha()
    {
        $request = new CriaContaRequest();
        $rules = $request->rules();

        $validator = Validator::make(
            ['nome_titular' => null],
            ['nome_titular' => $rules['nome_titular']]
        );
        $this->assertFalse($validator->passes());
    }

    # sail artisan test --filter=CriaContaRequestTest::test_nome_titular_obrigatorio_vazio_falha
    public function test_nome_titular_obrigatorio_vazio_falha()
    {
        $request = new CriaContaRequest();
        $rules = $request->rules();

        $validator = Validator::make(
            ['nome_titular' => ''],
            ['nome_titular' => $rules['nome_titular']]
        );
        $this->assertFalse($validator->passes());
    }

    # sail artisan test --filter=CriaContaRequestTest::test_nome_titular_apenas_letras_espacos_valido_passa
    public function test_nome_titular_apenas_letras_espacos_valido_passa()
    {
        $request = new CriaContaRequest();
        $rules = $request->rules();

        $validator = Validator::make(
            ['nome_titular' => 'João Silva'],
            ['nome_titular' => $rules['nome_titular']]
        );
        $this->assertTrue($validator->passes());
    }

    # sail artisan test --filter=CriaContaRequestTest::test_nome_titular_apenas_letras_espacos_invalido_numeros_falha
    public function test_nome_titular_apenas_letras_espacos_invalido_numeros_falha()
    {
        $request = new CriaContaRequest();
        $rules = $request->rules();

        $validator = Validator::make(
            ['nome_titular' => 'João123'],
            ['nome_titular' => $rules['nome_titular']]
        );
        $this->assertFalse($validator->passes());
    }

    # sail artisan test --filter=CriaContaRequestTest::test_nome_titular_apenas_letras_espacos_invalido_caracteres_falha
    public function test_nome_titular_apenas_letras_espacos_invalido_caracteres_falha()
    {
        $request = new CriaContaRequest();
        $rules = $request->rules();

        $validator = Validator::make(
            ['nome_titular' => 'João!'],
            ['nome_titular' => $rules['nome_titular']]
        );
        $this->assertFalse($validator->passes());
    }

    # sail artisan test --filter=CriaContaRequestTest::test_nome_titular_valido_com_espaco_passa
    public function test_nome_titular_valido_com_espaco_passa()
    {
        $request = new CriaContaRequest();
        $rules = $request->rules();

        $validator = Validator::make(
            ['nome_titular' => 'Pedro Henrique'],
            ['nome_titular' => $rules['nome_titular']]
        );
        $this->assertTrue($validator->passes());
    }

    # sail artisan test --filter=CriaContaRequestTest::test_nome_titular_valido_sem_espaco_passa
    public function test_nome_titular_valido_sem_espaco_passa()
    {
        $request = new CriaContaRequest();
        $rules = $request->rules();

        $validator = Validator::make(
            ['nome_titular' => 'Pedro'],
            ['nome_titular' => $rules['nome_titular']]
        );
        $this->assertTrue($validator->passes());
    }

    # sail artisan test --filter=CriaContaRequestTest::test_cpf_obrigatorio_null_falha
    public function test_cpf_obrigatorio_null_falha()
    {
        $request = new CriaContaRequest();
        $rules = $request->rules();

        $validator = Validator::make(
            ['cpf' => null],
            ['cpf' => $rules['cpf']]
        );
        $this->assertFalse($validator->passes());
    }

    # sail artisan test --filter=CriaContaRequestTest::test_cpf_obrigatorio_vazio_falha
    public function test_cpf_obrigatorio_vazio_falha()
    {
        $request = new CriaContaRequest();
        $rules = $request->rules();

        $validator = Validator::make(
            ['cpf' => ''],
            ['cpf' => $rules['cpf']]
        );
        $this->assertFalse($validator->passes());
    }

    # sail artisan test --filter=CriaContaRequestTest::test_cpf_exatamente_11_caracteres_valido_passa
    public function test_cpf_exatamente_11_caracteres_valido_passa()
    {
        $request = new CriaContaRequest();
        $rules = $request->rules();

        $validator = Validator::make(
            ['cpf' => '12345678901'],
            ['cpf' => $rules['cpf']]
        );
        $this->assertTrue($validator->passes());
    }

    # sail artisan test --filter=CriaContaRequestTest::test_cpf_exatamente_11_caracteres_invalido_curto_falha
    public function test_cpf_exatamente_11_caracteres_invalido_curto_falha()
    {
        $request = new CriaContaRequest();
        $rules = $request->rules();

        $validator = Validator::make(
            ['cpf' => '1234567890'], // 10 characters
            ['cpf' => $rules['cpf']]
        );
        $this->assertFalse($validator->passes());
    }

    # sail artisan test --filter=CriaContaRequestTest::test_cpf_exatamente_11_caracteres_invalido_longo_falha
    public function test_cpf_exatamente_11_caracteres_invalido_longo_falha()
    {
        $request = new CriaContaRequest();
        $rules = $request->rules();

        $validator = Validator::make(
            ['cpf' => '123456789012'], // 12 characters
            ['cpf' => $rules['cpf']]
        );
        $this->assertFalse($validator->passes());
    }

    # sail artisan test --filter=CriaContaRequestTest::test_cpf_apenas_digitos_valido_passa
    public function test_cpf_apenas_digitos_valido_passa()
    {
        $request = new CriaContaRequest();
        $rules = $request->rules();

        $validator = Validator::make(
            ['cpf' => '12345678901'],
            ['cpf' => $rules['cpf']]
        );
        $this->assertTrue($validator->passes());
    }

    # sail artisan test --filter=CriaContaRequestTest::test_cpf_apenas_digitos_invalido_letra_falha
    public function test_cpf_apenas_digitos_invalido_letra_falha()
    {
        $request = new CriaContaRequest();
        $rules = $request->rules();

        $validator = Validator::make(
            ['cpf' => '1234567890A'],
            ['cpf' => $rules['cpf']]
        );
        $this->assertFalse($validator->passes());
    }

    # sail artisan test --filter=CriaContaRequestTest::test_cpf_apenas_digitos_invalido_caracteres_falha
    public function test_cpf_apenas_digitos_invalido_caracteres_falha()
    {
        $request = new CriaContaRequest();
        $rules = $request->rules();

        $validator = Validator::make(
            ['cpf' => '12345-678901'],
            ['cpf' => $rules['cpf']]
        );
        $this->assertFalse($validator->passes());
    }

    # sail artisan test --filter=CriaContaRequestTest::test_cpf_valido_passa
    public function test_cpf_valido_passa()
    {
        $request = new CriaContaRequest();
        $rules = $request->rules();

        $validator = Validator::make(
            ['cpf' => '98765432100'],
            ['cpf' => $rules['cpf']]
        );
        $this->assertTrue($validator->passes());
    }
}
