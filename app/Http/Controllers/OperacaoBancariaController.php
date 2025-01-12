<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;

use App\Services\CotacaoMoedaService;
use App\Services\ListaMoedasDisponiveisService;

use App\Repositories\RepositoryInterface;

use App\Http\Requests\TransacaoRequest;
use App\Http\Requests\ContaIdRequest;
use App\Http\Requests\MoedaRequest;

class OperacaoBancariaController extends Controller
{
    protected $repositorySaldoConta;
    protected $repositoryTransacao;

    protected $cotacaoMoedaService;
    protected $listaMoedasDisponiveisService;

    // Injeção de dependência no construtor
    public function __construct()
    {
        $this->repositorySaldoConta = app(RepositoryInterface::class . '.saldo');
        $this->repositoryTransacao = app(RepositoryInterface::class . '.transacao');
        $this->cotacaoMoedaService = new CotacaoMoedaService();
        $this->listaMoedasDisponiveisService = new ListaMoedasDisponiveisService();
    }

    public function saldo(ContaIdRequest $contaIdRequest, MoedaRequest $moedaRequest)
    {
        $contas_id = $contaIdRequest->route('contas_id'); // Captura o parâmetro da rota
        $moeda = $moedaRequest->route('moeda'); // Captura o parâmetro da rota (se fornecido)

        try {

            if ($moeda != null) {
                $this->listaMoedasDisponiveisService->verificaMoedaDisponivel($moeda);
            }

            $saldoPorMoeda = $this->repositorySaldoConta->getAllByAccount($contas_id);

            if ($moeda == null) {
                return response()->json($saldoPorMoeda);
            } else {
                $somaSaldoConvertido = $this->somaMoedasConvertidas($saldoPorMoeda, $moeda);

                return response()->json([
                    'message' => 'Saldo Total da conta na moeda:',
                    'moeda' => $moeda,
                    'saldo' => number_format($somaSaldoConvertido, 2)
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao realizar a consulta do saldo', 'message' => $e->getMessage()], 500);
        }
    }

    public function deposito(TransacaoRequest $request)
    {
        try {

            $this->listaMoedasDisponiveisService->verificaMoedaDisponivel($request->moeda);


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

    public function saque(TransacaoRequest $request)
    {
        try {
            $this->listaMoedasDisponiveisService->verificaMoedaDisponivel($request->moeda);

            $saldoMoeda = $this->repositorySaldoConta->buscaMoedaEmConta($request->contas_id, $request->moeda);
            $novoSaldo = request()->all();

            if ($saldoMoeda === null || $saldoMoeda->valor < $request->valor) {
                $saldoPorMoeda = $this->repositorySaldoConta->getAllByAccount($request->contas_id);
                $somaSaldoConvertido = $this->somaMoedasConvertidas($saldoPorMoeda, $request->moeda);

                if ($somaSaldoConvertido < $request->valor && $somaSaldoConvertido != null) {
                    $responseData = $this->respostaSaldoInsuficiente($request);
                    return response()->json($responseData, 400);
                } else {

                    $novoSaldo['valor'] = $somaSaldoConvertido - $request->valor;

                    $this->repositorySaldoConta->deleteAllByAccount($request->contas_id);

                    $novoSaldo = $this->repositorySaldoConta->store(data: $novoSaldo);
                }
            } else {
                $novoSaldo['valor'] = $saldoMoeda->valor - $request->valor;
                $novoSaldo = $this->repositorySaldoConta->update(data: $novoSaldo);
            }

            $transacao = $this->repositoryTransacao->store($request->all(), 'saque');

            $responseData = $this->respostaTransacao(
                $transacao,
                $novoSaldo,
                'Saque realizado com sucesso'
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

    public function somaMoedasConvertidas(Collection $saldoPorMoeda, $moeda)
    {
        $somaSaldoConvertido = 0;

        foreach ($saldoPorMoeda as $saldo) {
            $somaSaldoConvertido += $this->realizaConversaoDeMoeda(
                $saldo->valor,
                $saldo->moeda,
                $moeda,
            );
        }
        return $somaSaldoConvertido;
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

    public function respostaSaldoInsuficiente($request)
    {
        return response()->json(
            [
                'error' => 'Saldo insuficiente para realizar a transação',
                'message' => 'Tente escolher um valor menor para saque: '
            ],
            400
        );
    }
}