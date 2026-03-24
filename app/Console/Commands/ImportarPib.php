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

        $traducaoPaises = [
            'Monaco'                    => 'Mônaco',
            'Liechtenstein'             => 'Liechtenstein',
            'Luxembourg'                => 'Luxemburgo',
            'Bermuda'                   => 'Bermudas',
            'Norway'                    => 'Noruega',
            'Ireland'                   => 'Irlanda',
            'Switzerland'               => 'Suíça',
            'Cayman Islands'            => 'Ilhas Cayman',
            'Singapore'                 => 'Singapura',
            'Qatar'                     => 'Catar',
            'United States'             => 'Estados Unidos',
            'Denmark'                   => 'Dinamarca',
            'Iceland'                   => 'Islândia',
            'Australia'                 => 'Austrália',
            'Netherlands'               => 'Holanda',
            'Sweden'                    => 'Suécia',
            'Finland'                   => 'Finlândia',
            'Austria'                   => 'Áustria',
            'Germany'                   => 'Alemanha',
            'Belgium'                   => 'Bélgica',
            'United Kingdom'            => 'Reino Unido',
            'France'                    => 'França',
            'Canada'                    => 'Canadá',
            'New Zealand'               => 'Nova Zelândia',
            'Japan'                     => 'Japão',
            'Israel'                    => 'Israel',
            'Italy'                     => 'Itália',
            'Spain'                     => 'Espanha',
            'South Korea'               => 'Coreia do Sul',
            'Korea, Rep.'               => 'Coreia do Sul',
            'China'                     => 'China',
            'Brazil'                    => 'Brasil',
            'Russia'                    => 'Rússia',
            'Russian Federation'        => 'Rússia',
            'India'                     => 'Índia',
            'Mexico'                    => 'México',
            'Argentina'                 => 'Argentina',
            'South Africa'              => 'África do Sul',
            'Egypt, Arab Rep.'          => 'Egito',
            'Iran, Islamic Rep.'        => 'Irã',
            'Saudi Arabia'              => 'Arábia Saudita',
            'United Arab Emirates'      => 'Emirados Árabes',
            'Turkey'                    => 'Turquia',
            'Turkiye'                   => 'Turquia',
            'Indonesia'                 => 'Indonésia',
            'Nigeria'                   => 'Nigéria',
            'Pakistan'                  => 'Paquistão',
            'Bangladesh'                => 'Bangladesh',
            'Colombia'                  => 'Colômbia',
            'Chile'                     => 'Chile',
            'Peru'                      => 'Peru',
            'Venezuela, RB'             => 'Venezuela',
            'Ecuador'                   => 'Equador',
            'Bolivia'                   => 'Bolívia',
            'Paraguay'                  => 'Paraguai',
            'Uruguay'                   => 'Uruguai',
            'Portugal'                  => 'Portugal',
            'Greece'                    => 'Grécia',
            'Poland'                    => 'Polônia',
            'Czech Republic'            => 'República Tcheca',
            'Czechia'                   => 'República Tcheca',
            'Hungary'                   => 'Hungria',
            'Romania'                   => 'Romênia',
            'Ukraine'                   => 'Ucrânia',
            'Philippines'               => 'Filipinas',
            'Vietnam'                   => 'Vietnã',
            'Viet Nam'                  => 'Vietnã',
            'Thailand'                  => 'Tailândia',
            'Malaysia'                  => 'Malásia',
            'Morocco'                   => 'Marrocos',
            'Kenya'                     => 'Quênia',
            'Ethiopia'                  => 'Etiópia',
            'Ghana'                     => 'Gana',
            'Tanzania'                  => 'Tanzânia',
            'Uganda'                    => 'Uganda',
            'Mozambique'                => 'Moçambique',
            'Angola'                    => 'Angola',
            'Cameroon'                  => 'Camarões',
            'Senegal'                   => 'Senegal',
            'Zambia'                    => 'Zâmbia',
            'Zimbabwe'                  => 'Zimbábue',
            'Dominican Republic'        => 'República Dominicana',
            'Guatemala'                 => 'Guatemala',
            'Honduras'                  => 'Honduras',
            'El Salvador'               => 'El Salvador',
            'Costa Rica'                => 'Costa Rica',
            'Panama'                    => 'Panamá',
            'Cuba'                      => 'Cuba',
            'Haiti'                     => 'Haiti',
            'Jamaica'                   => 'Jamaica',
            'Trinidad and Tobago'       => 'Trinidad e Tobago',
            'Bahamas, The'              => 'Bahamas',
            'Barbados'                  => 'Barbados',
            'Puerto Rico'               => 'Porto Rico',
            'Switzerland'               => 'Suíça',
            'Slovak Republic'           => 'Eslováquia',
            'Croatia'                   => 'Croácia',
            'Serbia'                    => 'Sérvia',
            'Bulgaria'                  => 'Bulgária',
            'Lithuania'                 => 'Lituânia',
            'Latvia'                    => 'Letônia',
            'Estonia'                   => 'Estônia',
            'Slovenia'                  => 'Eslovênia',
            'Luxembourg'                => 'Luxemburgo',
            'Malta'                     => 'Malta',
            'Cyprus'                    => 'Chipre',
            'Albania'                   => 'Albânia',
            'Jordan'                    => 'Jordânia',
            'Lebanon'                   => 'Líbano',
            'Iraq'                      => 'Iraque',
            'Kuwait'                    => 'Kuwait',
            'Bahrain'                   => 'Bahrein',
            'Oman'                      => 'Omã',
            'Yemen, Rep.'               => 'Iêmen',
            'Afghanistan'               => 'Afeganistão',
            'Myanmar'                   => 'Mianmar',
            'Cambodia'                  => 'Camboja',
            'Sri Lanka'                 => 'Sri Lanka',
            'Nepal'                     => 'Nepal',
            'Maldives'                  => 'Maldivas',
            'Fiji'                      => 'Fiji',
            'Papua New Guinea'          => 'Papua Nova Guiné',
            'New Caledonia'             => 'Nova Caledônia',
            'Virgin Islands (U.S.)'     => 'Ilhas Virgens (EUA)',
            'Greenland'                 => 'Groenlândia',
            'Faroe Islands'             => 'Ilhas Faroé',
            'West Bank and Gaza'        => 'Cisjordânia e Gaza',
            'Congo, Dem. Rep.'          => 'Congo (RDC)',
            'Congo, Rep.'               => 'Congo',
            "Cote d'Ivoire"             => 'Costa do Marfim',
            'Burkina Faso'              => 'Burkina Faso',
            'Mali'                      => 'Mali',
            'Niger'                     => 'Níger',
            'Chad'                      => 'Chade',
            'Sudan'                     => 'Sudão',
            'Somalia'                   => 'Somália',
            'Burundi'                   => 'Burundi',
            'Rwanda'                    => 'Ruanda',
            'Malawi'                    => 'Malawi',
            'Madagascar'                => 'Madagascar',
            'Mauritius'                 => 'Maurício',
            'Seychelles'                => 'Seicheles',
        ];
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

        $codigosIgnorar = ['MEA', 'MNA', 'EAP', 'ECA', 'LCN', 'SAS', 'SSA', 'SSF', 'WLD', 'EUU', 'OED', 'HIC', 'MIC', 'LMC', 'UMC', 'LIC', 'NAC', 'INX', 'IBD', 'IBT', 'IDB', 'IDX', 'IDA'];

        foreach ($paises as $item) {
            // 1 — verifica se tem o campo de código
            if (empty($item['countryiso3code']) || strlen($item['countryiso3code']) !== 3) {
                $ignorados++;
                continue;
            }
        
            // 2 — agora sim define $codigo
            $codigo = $item['countryiso3code'];
        
            // 3 — verifica se é um agregado regional
            if (in_array($codigo, $codigosIgnorar)) {
                $ignorados++;
                continue;
            }
        
            // 4 — verifica se tem valor de PIB
            if (empty($item['value'])) {
                $ignorados++;
                continue;
            }
        
            $nomeOriginal = $item['country']['value'];
            $nomeFinal    = $traducaoPaises[$nomeOriginal] ?? $nomeOriginal;
            $regiao       = $regioes[$codigo] ?? 'Outras regiões';
        
            Pais::updateOrCreate(
                ['codigo' => $codigo, 'ano' => $ano],
                [
                    'nome'           => $nomeFinal,
                    'regiao'         => $regiao,
                    'pib_per_capita' => $item['value'],
                ]
            );
        
            $importados++;
        }

        $this->info("Concluído! {$importados} países importados, {$ignorados} ignorados.");
    }
}