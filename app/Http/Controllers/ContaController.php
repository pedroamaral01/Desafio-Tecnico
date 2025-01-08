<?php

namespace App\Http\Controllers;

use App\Http\Requests\CriaContaRequest;
use Illuminate\Http\Request;

use App\Repositories\ContaRepository;

class ContaController extends Controller
{
    protected $repository;

    public function __construct()
    {
        $this->repository = new ContaRepository();
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
            return response()->json($conta, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
