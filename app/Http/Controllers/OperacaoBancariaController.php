<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;

use App\Services\CotacaoMoedaService;
use App\Services\ListaMoedasDisponiveisService;

use App\Repositories\SaldoContaRepositoryInterface;
use App\Repositories\TransacaoRepositoryInterface;
use App\Repositories\ContaRepositoryInterface;

use App\Http\Requests\TransacaoRequest;
use App\Http\Requests\ContaIdRequest;
use App\Http\Requests\MoedaRequest;

class OperacaoBancariaController extends Controller
{
    protected $saldoContaRepository;

    protected $transacaoRepository;

    protected $contaRepository;

    protected $cotacaoMoedaService;

    protected $listaMoedasDisponiveisService;

    public function __construct(
        SaldoContaRepositoryInterface $saldoContaRepository,
        TransacaoRepositoryInterface $transacaoRepository,
        ContaRepositoryInterface $contaRepository,
        CotacaoMoedaService $cotacaoMoedaService,
        ListaMoedasDisponiveisService $listaMoedasDisponiveisService
    ) {
        $this->saldoContaRepository = $saldoContaRepository;
        $this->transacaoRepository = $transacaoRepository;
        $this->contaRepository = $contaRepository;
        $this->cotacaoMoedaService = $cotacaoMoedaService;
        $this->listaMoedasDisponiveisService = $listaMoedasDisponiveisService;
    }

    public function saldo(ContaIdRequest $contaIdRequest, MoedaRequest $moedaRequest)
    {
        $contas_id = $contaIdRequest->route('contas_id');
        $moeda = $moedaRequest->route('moeda');

        try {
            if ($this->contaRepository->find($contas_id) === null) {
                return response()->json(['error' => 'Conta não encontrada'], 404);
            }

            if ($moeda != null) {
                $this->listaMoedasDisponiveisService->verificaMoedaDisponivel($moeda);
            }

            $saldoPorMoeda = $this->saldoContaRepository->getAllByAccount($contas_id);

            if ($saldoPorMoeda->isEmpty()) {
                return response()->json(['message' => 'Essa conta não possui saldo'], 404);
            }

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
            if ($this->contaRepository->find($request->contas_id) === null) {
                return response()->json(['error' => 'Conta não encontrada'], 404);
            }

            $this->listaMoedasDisponiveisService->verificaMoedaDisponivel($request->moeda);


            $saldoMoeda = $this->saldoContaRepository->buscaMoedaEmConta($request->contas_id, $request->moeda);
            if ($saldoMoeda === null) {
                $saldoAtualizado = $this->saldoContaRepository->store(data: $request->all());
            } else {
                $saldoMoeda->valor += $request->valor;
                $saldoAtualizado = $this->saldoContaRepository->update(data: [
                    'contas_id' => $saldoMoeda->contas_id,
                    'moeda' => $saldoMoeda->moeda,
                    'valor' => $saldoMoeda->valor
                ]);
            }

            $transacao = $this->transacaoRepository->store($request->all(), 'deposito');

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

            $saldoMoeda = $this->saldoContaRepository->buscaMoedaEmConta($request->contas_id, $request->moeda);
            $novoSaldo = request()->all();

            if ($saldoMoeda === null || $saldoMoeda->valor < $request->valor) {
                $saldoPorMoeda = $this->saldoContaRepository->getAllByAccount($request->contas_id);
                $somaSaldoConvertido = $this->somaMoedasConvertidas($saldoPorMoeda, $request->moeda);

                if ($somaSaldoConvertido < $request->valor && $somaSaldoConvertido != null) {
                    $responseData = $this->respostaSaldoInsuficiente($request);
                    return response()->json($responseData, 400);
                } else {

                    $novoSaldo['valor'] = $somaSaldoConvertido - $request->valor;

                    $this->saldoContaRepository->deleteAllByAccount($request->contas_id);

                    $novoSaldo = $this->saldoContaRepository->store(data: $novoSaldo);
                }
            } else {
                $novoSaldo['valor'] = $saldoMoeda->valor - $request->valor;
                $novoSaldo = $this->saldoContaRepository->update(data: $novoSaldo);
            }

            $transacao = $this->transacaoRepository->store($request->all(), 'saque');

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