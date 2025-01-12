<?php

namespace App\Repositories;

interface ContaRepositoryInterface
{
    public function find($id);
    public function findAll();
    public function store(array $data);
}