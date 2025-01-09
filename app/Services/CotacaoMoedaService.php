<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class CotacaoMoedaService
{
    protected const baseUrl = "https://olinda.bcb.gov.br/olinda/servico/PTAX/versao/v1/odata/CotacaoMoedaDia(moeda=@moeda,dataCotacao=@dataCotacao)?";
    protected $baseUrlMoeda = "@moeda=";
    protected $baseUrlData = "&@dataCotacao=";
    protected const sufixoUrl = "&\$top=100&\$filter=tipoBoletim%20eq%20'Fechamento%20PTAX'&\$format=json&\$select=cotacaoCompra,cotacaoVenda,dataHoraCotacao,tipoBoletim";
    protected $moedaParametroUrl = "";
    protected $dataCotacaoParametroUrl = "";
    protected $moeda;
    protected $dataCotacao;
    protected $cotacaoCompra;
    protected $cotacaoVenda;

    public function getUrlCompleta()
    {
        return self::baseUrl . "$this->moedaParametroUrl$this->dataCotacaoParametroUrl" . self::sufixoUrl;
    }
    private function setMoedaParametroUrl($moeda)
    {
        $this->moedaParametroUrl = $this->baseUrlMoeda . "'" . strtoupper($moeda) . "'";
    }

    private function setDataParametroUrl($dataCotacao)
    {
        $this->dataCotacaoParametroUrl = $this->baseUrlData . "'" . $dataCotacao . "'";
    }

    public function getCotacaoApi()
    {
        return Http::get($this->getUrlCompleta());
    }

    private function existeCotacao()
    {
        return !empty($this->cotacaoCompra) && !empty($this->cotacaoVenda);
    }

    private function setCotacaoCompraeVenda()
    {
        $respostaApi = $this->getCotacaoAPi();
        if (!empty($respostaApi['value'])) {
            $this->cotacaoCompra = $respostaApi['value'][0]['cotacaoCompra'];
            $this->cotacaoVenda = $respostaApi['value'][0]['cotacaoVenda'];
        }
    }

    public function getCotacaoCompra()
    {
        return $this->cotacaoCompra;
    }

    public function getCotacaoVenda()
    {
        return $this->cotacaoVenda;
    }

    public function realizaCotacao($moeda)
    {
        $dataCotacao = date(format: 'Y-m-d');

        $this->setMoedaParametroUrl($moeda);

        $this->setDataParametroUrl((new \DateTime($dataCotacao))->format('m-d-Y'));

        $this->setCotacaoCompraeVenda();

        while (!$this->existeCotacao()) {
            $this->setCotacaoCompraeVenda();
            $dataCotacao = date('Y-m-d', timestamp: strtotime($dataCotacao . '-1 day'));
            $this->setDataParametroUrl((new \DateTime($dataCotacao))->format('m-d-Y'));
        }
    }
}