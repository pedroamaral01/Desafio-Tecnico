<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Repositories\SaldoContaRepository;
use App\Repositories\TransacaoRepository;

class OperacaoBancariaController extends Controller
{
    protected $repositorySaldoConta;

    protected $repositoryTransacao;

    public function __construct()
    {
        $this->repositorySaldoConta = new SaldoContaRepository();
        $this->repositoryTransacao = new TransacaoRepository();
    }
    public function deposito(Request $request)
    {
        try {
            $saldoMoeda = $this->repositorySaldoConta->buscaMoedaEmConta($request->contas_id, $request->moeda);
            if ($saldoMoeda === null) {
                $saldoAtualizado = $this->repositorySaldoConta->store(data: $request->all());
            } else {
                $saldoMoeda->valor += $request->valor;
                $saldoAtualizado = $this->repositorySaldoConta->update(data: [
                    'contas_id' => $saldoMoeda->contas_id,
                    'moeda' => $saldoMoeda->moeda,
                    'valor' => $saldoMoeda->valor
                ]);
            }

            $transacao = $this->repositoryTransacao->store($request->all(), 'deposito');

            $responseData = $this->respostaTransacao(
                $transacao,
                $saldoAtualizado,
                'Depósito realizado com sucesso'
            );
            return response()->json($responseData, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao realizar a transação', 'message' => $e->getMessage()], 500);
        }
    }

    public function respostaTransacao($transacao, $saldoAtualizado, $mensagem)
    {
        return [
            'mensagem' => $mensagem,
            'transacao' => [
                'valor' => $transacao->valor,
                'moeda' => $transacao->moeda,
                'tipo_transacao' => $transacao->tipo_transacao,
                'data' => $transacao->created_at,
            ],
            'saldo após transação' => [
                'contas_id' => $saldoAtualizado->contas_id,
                'moeda' => $saldoAtualizado->moeda,
                'valor' => $saldoAtualizado->valor,
            ],
        ];
    }
}