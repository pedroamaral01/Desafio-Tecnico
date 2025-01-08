<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CriaContaRequest extends FormRequest
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
            'nome_titular' => 'required|string|alpha:ascii',
            'cpf' => 'required|string|size:11',
        ];
    }

    public function messages(): array
    {
        return [
            'nome_titular.required' => 'O campo nome do titular é obrigatório',
            'nome_titular.alpha' => 'O campo nome do titular deve conter apenas letras',
            'nome_titular.ascii' => 'O campo nome do titular deve conter apenas caracteres ASCII',
            'cpf.required' => 'O campo CPF é obrigatório',
            'cpf.size' => 'O campo CPF deve conter 11 caracteres',
        ];
    }
}