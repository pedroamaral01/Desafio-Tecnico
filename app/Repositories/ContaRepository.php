<?php

namespace App\Repositories;

use App\Models\Conta;

class ContaRepository implements RepositoryInterface
{
    protected $model;
    public function __construct()
    {
        $this->model = Conta::class;
    }

    public function findAll()
    {
        return $this->model::all();
    }

    public function store(array $data)
    {
        $conta = new Conta();
        $conta->fill(attributes: $data);
        $conta->save();
        return $conta;
    }
}