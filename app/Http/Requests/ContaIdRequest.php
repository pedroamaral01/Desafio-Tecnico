<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContaIdRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'contas_id' => 'required|integer|min:1', // Obrigatório, inteiro e positivo
        ];
    }

    public function messages()
    {
        return [
            'contas_id.required' => 'O campo contas_id é obrigatório.',
            'contas_id.integer' => 'O campo contas_id deve ser um número inteiro.',
            'contas_id.min' => 'O campo contas_id deve ser um número positivo.',
        ];
    }

    public function validationData()
    {
        return array_merge($this->route()->parameters(), $this->all());
    }
}
