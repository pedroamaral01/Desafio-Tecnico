<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Repositories\SaldoContaRepository;
use App\Repositories\TransacaoRepository;

use App\Services\CotacaoMoedaService;

class OperacaoBancariaController extends Controller
{
    protected $repositorySaldoConta;

    protected $repositoryTransacao;

    protected $cotacaoMoedaService;

    public function __construct()
    {
        $this->repositorySaldoConta = new SaldoContaRepository();
        $this->repositoryTransacao = new TransacaoRepository();
        $this->cotacaoMoedaService = new CotacaoMoedaService();
    }

    public function saldo($contas_id, $moeda = null)
    {
        $somaSaldoConvertido = 0;
        try {
            $saldoNaConta = $this->repositorySaldoConta->getAllByAccount($contas_id);

            if ($moeda == null) {
                return response()->json($saldoNaConta);
            } else {
                foreach ($saldoNaConta as $saldo) {
                    $somaSaldoConvertido += $this->realizaConversaoDeMoeda(
                        $saldo->valor,
                        $saldo->moeda,
                        $moeda
                    );
                }

                return response()->json([
                    'message' => 'Saldo Total da conta na moeda:',
                    'moeda' => $moeda,
                    'saldo' => number_format($somaSaldoConvertido, 2)
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao realizar a consiulta do saldo', 'message' => $e->getMessage()], 500);
        }
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
            return response()->json(
                [
                    'error' => 'Erro ao realizar a transação',
                    'message' => $e->getMessage()
                ],
                500
            );
        }
    }

    public function realizaConversaoDeMoeda($saldoMoedaBase, $moedaBase, $moedaObjetivo)
    {
        try {

            if ($moedaBase == $moedaObjetivo) {
                return $saldoMoedaBase;
            }
            if ($moedaBase != 'BRL') {
                $this->cotacaoMoedaService->realizaCotacao($moedaBase);
                $saldoMoedaBase *= $this->cotacaoMoedaService->getCotacaoCompra();
            }
            if ($moedaObjetivo == 'BRL') {
                return $saldoMoedaBase;
            }
            $this->cotacaoMoedaService->realizaCotacao($moedaObjetivo);
            $saldoMoedaBase /= $this->cotacaoMoedaService->getCotacaoVenda();
            return $saldoMoedaBase;
        } catch (\Exception $e) {
            return response()->json(
                [
                    'error' => 'Erro ao realizar a conversão de moeda',
                    'message' => $e->getMessage()
                ],
                status: 500
            );
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