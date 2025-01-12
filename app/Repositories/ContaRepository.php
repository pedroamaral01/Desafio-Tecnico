<?php

namespace App\Repositories;

use App\Models\Conta;

class ContaRepository implements ContaRepositoryInterface
{
    protected $model;
    public function __construct()
    {
        $this->model = Conta::class;
    }
    public function find($id)
    {
        return $this->model::find($id);
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