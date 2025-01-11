<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransacaoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'contas_id' => 'required|integer|min:1',
            'valor' => 'required|numeric|regex:/^\d{1,8}(\.\d{1,2})?$/|min:0.01',
            'moeda' => 'required|string|regex:/^[A-Z]{3}$/',
        ];
    }

    public function messages()
    {
        return [
            // Mensagens para 'contas_id'
            'contas_id.required' => 'O campo Número da Conta é obrigatório.',
            'contas_id.integer' => 'O campo Número da Conta deve ser um número inteiro.',
            'contas_id.min' => 'O campo Número da Conta deve ser um número inteiro positivo.',

            // Mensagens para 'valor'
            'valor.required' => 'O campo Valor é obrigatório.',
            'valor.numeric' => 'O campo Valor deve ser um número.',
            'valor.regex' => 'O campo Valor deve estar no formato correto (até 8 dígitos antes do ponto e até 2 casas decimais).',
            'valor.min' => 'O campo Valor deve ser maior ou igual a 0.01.',

            // Mensagens para 'moeda'
            'moeda.required' => 'O campo Moeda é obrigatório.',
            'moeda.string' => 'O campo Moeda deve ser um texto.',
            'moeda.regex' => 'O campo Moeda deve conter exatamente 3 letras maiúsculas do alfabeto (ex: USD, BRL).',
        ];
    }
}
