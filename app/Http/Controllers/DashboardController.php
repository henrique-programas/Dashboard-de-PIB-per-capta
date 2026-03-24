<?php

namespace App\Http\Controllers;

use App\Models\Pais;

class DashboardController extends Controller
{
    public function index()
    {
        
        // mapa de tradução das regiões
        $traducaoRegioes = [
            'Latin America & Caribbean '                             => 'América Latina e Caribe',
            'Sub-Saharan Africa'                                     => 'África Subsaariana',
            'Sub-Saharan Africa '                                    => 'África Subsaariana',
            'Sub-Saharan Africa'                                     => 'África Subsaariana',
            'Aggregates'                                             => 'Agregados',
            'North America'                                          => 'América do Norte',
            'Latin America & Caribbean'                              => 'América Latina e Caribe',
            'Europe & Central Asia'                                  => 'Europa e Ásia Central',
            'Middle East, North Africa, Afghanistan & Pakistan'      => 'Oriente Médio e Norte da África',
            'East Asia & Pacific'                                    => 'Ásia Oriental e Pacífico',
            'South Asia'                                             => 'Ásia do Sul',
            'Sub-Saharan Africa'                                     => 'África Subsaariana',
            'Aggregates'                                             => 'Agregados',
            'Outras regiões'                                         => 'Outras regiões',
            ];
            
            // busca todos os países e traduz a região de cada um
        $paises = Pais::where('ano', 2022)
        ->orderBy('pib_per_capita', 'desc')
        ->get()
                      ->map(function ($pais) use ($traducaoRegioes) {
                          $pais->regiao = $traducaoRegioes[$pais->regiao] ?? $pais->regiao;
                          return $pais;
                          });
                          
                          $top10 = $paises->take(10);
                          
                          $maior  = $paises->first();
                          $menor  = $paises->last();
                          $media  = Pais::where('ano', 2022)->avg('pib_per_capita');
        $brasil = $paises->firstWhere('codigo', 'BRA');
        
        $paisesMap = $paises->mapWithKeys(function ($p) {
            return [$p->codigo => [
                'nome'   => $p->nome,
                'pib'    => $p->pib_per_capita,
                'regiao' => $p->regiao,
            ]];
        });
        
        $porRegiao = Pais::where('ano', 2022)
        ->selectRaw('regiao, COUNT(*) as total, AVG(pib_per_capita) as media_pib')
        ->groupBy('regiao')
        ->orderBy('media_pib', 'desc')
        ->get()
        ->map(function ($r) use ($traducaoRegioes) {
            $r->regiao = $traducaoRegioes[$r->regiao] ?? $r->regiao;
            return $r;
            })
            ->filter(function ($r) {
                // remove "Agregados" — não é uma região geográfica real
                return $r->regiao !== 'Agregados';
                });
                
                return view('home', [
                    'paises'    => $paises,
                    'top10'     => $top10,
                    'maior'     => $maior,
                    'menor'     => $menor,
                    'media'     => $media,
                    'brasil'    => $brasil,
                    'porRegiao' => $porRegiao,
                    'paisesMap' => $paisesMap,
                    ]);
    }
}