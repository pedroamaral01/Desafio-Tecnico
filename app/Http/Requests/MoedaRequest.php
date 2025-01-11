<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MoedaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'moeda' => 'nullable|string|size:3|regex:/^[A-Z]{3}$/',
        ];
    }

    public function messages()
    {
        return [
            'moeda.required' => 'O campo Moeda é obrigatório.',
            'moeda.string' => 'O campo Moeda deve ser um texto.',
            'moeda.regex' => 'O campo Moeda deve conter exatamente 3 letras maiúsculas do alfabeto (ex: USD, BRL).'
        ];
    }

    public function validationData()
    {
        return array_merge($this->route()->parameters(), $this->all());
    }
}
