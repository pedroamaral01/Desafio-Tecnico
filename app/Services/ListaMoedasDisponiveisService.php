<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ListaMoedasDisponiveisService
{
    protected const URL = "https://olinda.bcb.gov.br/olinda/servico/PTAX/versao/v1/odata/Moedas?\$top=100&\$format=json&\$select=simbolo";

    public function verificaMoedaDisponivel(string $moeda)
    {
        $response = Http::get(self::URL);

        if ($response->failed()) {
            throw new \Exception('Erro ao consultar a lista de moedas disponíveis');
        }

        $moedas = collect($response->json('value'))->pluck('simbolo');

        if (!$moedas->contains(strtoupper($moeda))) {
            throw new \Exception('Moeda não disponível na lista de cotações diárias e Taxas de Câmbio');
        }

        return true;
    }
}
