<?php

namespace App\Http\Controllers;

use App\Models\Pais;

class DashboardController extends Controller
{
    public function index()
    {
        $paises = Pais::orderBy('pib_per_capita', 'desc')->get();

        $maior = Pais::orderBy('pib_per_capita', 'desc')->first();

        $menor = Pais::orderBy('pib_per_capita', 'asc')->first();

        $media = Pais::avg('pib_per_capita');

        $brasil = Pais::where('codigo', 'BRA')->first();

        return view('home', [
            'paises' => $paises,
            'maior'  => $maior,
            'menor'  => $menor,
            'media'  => $media,
            'brasil' => $brasil,
        ]);
    }
}