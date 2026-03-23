<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Pais;

class ImportarPib extends Command
{
    protected $signature = 'pib:importar {ano=2022}';
    protected $description = 'Importa dados de PIB per capita da API do Banco Mundial';

    public function handle()
    {
        $ano = $this->argument('ano');

        // passo 1 — busca as regiões de cada país
        $this->info('Buscando regiões dos países...');

        $respostaRegioes = Http::get('https://api.worldbank.org/v2/country/all', [
            'format'   => 'json',
            'per_page' => 300,
        ]);

        // monta um dicionário: codigo => regiao
        // ex: ['BRA' => 'Latin America & Caribbean', 'USA' => 'North America']
        $regioes = [];
        foreach ($respostaRegioes->json()[1] ?? [] as $item) {
            // era $item['iso3Code'] — o campo certo é $item['id']
            if (!empty($item['id']) && !empty($item['region']['value'])) {
                $regioes[$item['id']] = $item['region']['value'];
            }
        }

        $this->info(count($regioes) . ' regiões carregadas.');

        // passo 2 — busca os dados de PIB
        $this->info("Buscando PIB per capita do ano {$ano}...");

        $respostaPib = Http::get('https://api.worldbank.org/v2/country/all/indicator/NY.GDP.PCAP.CD', [
            'format'   => 'json',
            'date'     => $ano,
            'per_page' => 300,
        ]);

        if ($respostaPib->failed()) {
            $this->error('Erro ao conectar na API do Banco Mundial.');
            return;
        }

        $paises = $respostaPib->json()[1] ?? [];

        if (empty($paises)) {
            $this->error('Nenhum dado retornado pela API.');
            return;
        }

        // passo 3 — salva no banco
        $importados = 0;
        $ignorados  = 0;

        foreach ($paises as $item) {
            if (empty($item['value'])) {
                $ignorados++;
                continue;
            }

            if (empty($item['countryiso3code']) || strlen($item['countryiso3code']) !== 3) {
                $ignorados++;
                continue;
            }

            $codigo = $item['countryiso3code'];

            // busca a região no dicionário que montamos
            // se não encontrar, usa 'Outras regiões'
            $regiao = $regioes[$codigo] ?? 'Outras regiões';

            Pais::updateOrCreate(
                [
                    'codigo' => $codigo,
                    'ano'    => $ano,
                ],
                [
                    'nome'           => $item['country']['value'],
                    'regiao'         => $regiao,
                    'pib_per_capita' => $item['value'],
                ]
            );

            $importados++;
        }

        $this->info("Concluído! {$importados} países importados, {$ignorados} ignorados.");
    }
}