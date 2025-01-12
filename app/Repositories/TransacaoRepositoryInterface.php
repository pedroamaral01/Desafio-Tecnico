<?php

namespace App\Repositories;

interface TransacaoRepositoryInterface
{
    public function store(array $data, $tipo_transacao);
}