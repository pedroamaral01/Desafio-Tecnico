<?php

namespace App\Repositories;

use App\Models\Transacao;

class TransacaoRepository implements RepositoryInterface
{
    protected $model;

    public function __construct()
    {
        $this->model = Transacao::class;
    }

    public function store(array $data, $tipo_transacao)
    {
        $data['tipo_transacao'] = $tipo_transacao;
        $transacao = new Transacao();
        $transacao->fill(attributes: $data);
        $transacao->save();
        return $transacao;
    }
}