<?php

namespace App\Http\Controllers;

use App\Models\Pais;

class DashboardController extends Controller
{
    public function index()
    {
        // top 10 para o gráfico e ranking
        $top10 = Pais::where('ano', 2022)
                     ->orderBy('pib_per_capita', 'desc')
                     ->limit(10)
                     ->get();

        // todos os países para a tabela
        $paises = Pais::where('ano', 2022)
                      ->orderBy('pib_per_capita', 'desc')
                      ->get();

        $maior  = $paises->first();
        $menor  = Pais::where('ano', 2022)
                      ->orderBy('pib_per_capita', 'asc')
                      ->first();
        $media  = Pais::where('ano', 2022)->avg('pib_per_capita');
        $brasil = Pais::where('codigo', 'BRA')->where('ano', 2022)->first();

        // conta quantos países por região para o gráfico de rosca
        $porRegiao = Pais::where('ano', 2022)
                         ->selectRaw('regiao, COUNT(*) as total, AVG(pib_per_capita) as media_pib')
                         ->groupBy('regiao')
                         ->orderBy('media_pib', 'desc')
                         ->get();

        return view('home', [
            'paises'    => $paises,
            'top10'     => $top10,
            'maior'     => $maior,
            'menor'     => $menor,
            'media'     => $media,
            'brasil'    => $brasil,
            'porRegiao' => $porRegiao,
        ]);
    }
}