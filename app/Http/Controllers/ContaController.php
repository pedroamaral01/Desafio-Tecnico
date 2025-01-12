<?php

namespace App\Http\Controllers;

use App\Http\Requests\CriaContaRequest;
use Illuminate\Http\Request;

use App\Repositories\ContaRepositoryInterface;

class ContaController extends Controller
{
    protected $repository;

    public function __construct()
    {
        $this->repository = app(ContaRepositoryInterface::class);
    }

    public function index()
    {
        try {
            $contas = $this->repository->findAll();
            return response()->json($contas);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function store(CriaContaRequest $request)
    {
        try {
            $conta = $this->repository->store($request->all());

            $response = [
                'mensagem' => 'Conta criada com sucesso',
                'dados' => [
                    'id' => $conta->id,
                    'nome_titular' => $conta->nome_titular,
                    'cpf' => $conta->cpf,
                    'data' => $conta->created_at,
                ],
            ];

            return response()->json($response, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
