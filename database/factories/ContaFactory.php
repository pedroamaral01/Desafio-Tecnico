<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Conta;

class ContaFactory extends Factory
{
    protected $model = Conta::class;

    public function definition()
    {
        return [
            'nome_titular' => $this->faker->name,
            'cpf' => $this->faker->numerify('12345678912'), // CPF fictício
        ];
    }
}
