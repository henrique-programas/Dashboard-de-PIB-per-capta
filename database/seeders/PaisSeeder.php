<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pais;

class PaisSeeder extends Seeder
{
    public function run(): void
    {
        $paises = [
            ['nome' => 'Luxemburgo',     'codigo' => 'LUX', 'regiao' => 'Europa',           'ano' => 2022, 'pib_per_capita' => 105726],
            ['nome' => 'Suíça',          'codigo' => 'CHE', 'regiao' => 'Europa',           'ano' => 2022, 'pib_per_capita' => 92434],
            ['nome' => 'Noruega',        'codigo' => 'NOR', 'regiao' => 'Europa',           'ano' => 2022, 'pib_per_capita' => 89154],
            ['nome' => 'EUA',            'codigo' => 'USA', 'regiao' => 'América do Norte', 'ano' => 2022, 'pib_per_capita' => 76329],
            ['nome' => 'Brasil',         'codigo' => 'BRA', 'regiao' => 'América Latina',   'ano' => 2022, 'pib_per_capita' => 9673],
            ['nome' => 'China',          'codigo' => 'CHN', 'regiao' => 'Ásia',             'ano' => 2022, 'pib_per_capita' => 12720],
            ['nome' => 'Índia',          'codigo' => 'IND', 'regiao' => 'Ásia',             'ano' => 2022, 'pib_per_capita' => 2389],
            ['nome' => 'África do Sul',  'codigo' => 'ZAF', 'regiao' => 'África',           'ano' => 2022, 'pib_per_capita' => 6773],
            ['nome' => 'Argentina',      'codigo' => 'ARG', 'regiao' => 'América Latina',   'ano' => 2022, 'pib_per_capita' => 9122],
            ['nome' => 'Burundi',        'codigo' => 'BDI', 'regiao' => 'África',           'ano' => 2022, 'pib_per_capita' => 221],
        ];

        foreach ($paises as $pais) {
            Pais::create($pais);
        }
    }
}