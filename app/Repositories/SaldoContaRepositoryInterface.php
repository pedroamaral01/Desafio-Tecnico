<?php

namespace App\Repositories;

interface SaldoContaRepositoryInterface
{
    public function buscaMoedaEmConta($contas_id, $moeda);

    public function store(array $data);

    public function update(array $data);

    public function getAllByAccount($contas_id);

    public function deleteAllByAccount($contas_id);
}