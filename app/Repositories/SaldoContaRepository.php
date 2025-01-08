<?php

namespace App\Repositories;

use App\Models\SaldoConta;

class SaldoContaRepository
{
    protected $model;

    public function __construct()
    {
        $this->model = SaldoConta::class;
    }

    public function buscaMoedaEmConta($contas_id, $moeda)
    {
        return $this->model::where('contas_id', $contas_id)
            ->where('moeda', $moeda)
            ->first();
    }

    public function store(array $data)
    {
        $saldoConta = new SaldoConta();
        $saldoConta->fill($data);
        $saldoConta->save();
        return $saldoConta;
    }

    public function update(array $data)
    {
        $saldoConta = $this->buscaMoedaEmConta($data['contas_id'], $data['moeda']);
        $saldoConta->fill($data);
        $saldoConta->save();
        return $saldoConta;
    }
}