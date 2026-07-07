<?php

// Consulta a Exchange Rate API para saber quantos Kwanzas dao 1
// dolar e 1 euro. Guarda o resultado numa cache de 1 hora, para nao
// ter de bater na API sempre que alguem abre o dashboard.
class ExchangeRate
{
    private const URL = 'https://open.er-api.com/v6/latest/USD';
    private const FICHEIRO_CACHE = __DIR__ . '/../assets/cache/taxa_cambio.json';
    private const CACHE_MINUTOS = 60;

    // Devolve ['aoa_por_usd' => x, 'aoa_por_eur' => y], ou false se a
    // API estiver em baixo e nao houver cache para usar.
    public static function obterTaxas(): array|false
    {
        $cache = self::lerCache();
        if ($cache !== false) {
            return $cache;
        }

        $contexto = stream_context_create(['http' => ['timeout' => 5]]);
        $resposta = @file_get_contents(self::URL, false, $contexto);

        if ($resposta === false) {
            return false;
        }

        $dados = json_decode($resposta, true);

        if (!isset($dados['rates']['AOA'], $dados['rates']['EUR'])) {
            return false;
        }

        $taxas = [
            'aoa_por_usd' => $dados['rates']['AOA'],
            'aoa_por_eur' => $dados['rates']['AOA'] / $dados['rates']['EUR'],
        ];

        file_put_contents(self::FICHEIRO_CACHE, json_encode($taxas));

        return $taxas;
    }

    private static function lerCache(): array|false
    {
        if (!file_exists(self::FICHEIRO_CACHE)) {
            return false;
        }

        $idadeEmSegundos = time() - filemtime(self::FICHEIRO_CACHE);
        if ($idadeEmSegundos > self::CACHE_MINUTOS * 60) {
            return false;
        }

        $dados = json_decode(file_get_contents(self::FICHEIRO_CACHE), true);

        return $dados ?: false;
    }
}
