<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Dashboard PIB per capita</title>
    <style>
        body { font-family: sans-serif; padding: 2rem; background: #f5f5f5; }
        h1   { margin-bottom: 1.5rem; }

        .cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .card {
            background: white;
            border-radius: 8px;
            padding: 1.2rem;
            border: 1px solid #e0e0e0;
        }
        .card .label { font-size: 12px; color: #888; margin-bottom: 4px; }
        .card .valor { font-size: 22px; font-weight: bold; }
        .card .sub   { font-size: 13px; color: #555; margin-top: 4px; }

        table { width: 100%; background: white; border-radius: 8px;
                border-collapse: collapse; border: 1px solid #e0e0e0; }
        th, td { padding: 10px 14px; text-align: left; border-bottom: 1px solid #f0f0f0; }
        th { font-size: 12px; color: #888; font-weight: 600; }
        tr:last-child td { border-bottom: none; }
    </style>
</head>
<body>

    <h1>Dashboard PIB per capita mundial</h1>

    {{-- Cards de métricas --}}
    <div class="cards">
        <div class="card">
            <div class="label">Maior PIB per capita</div>
            <div class="valor">US$ {{ number_format($maior->pib_per_capita, 0, ',', '.') }}</div>
            <div class="sub">{{ $maior->nome }}</div>
        </div>
        <div class="card">
            <div class="label">Menor PIB per capita</div>
            <div class="valor">US$ {{ number_format($menor->pib_per_capita, 0, ',', '.') }}</div>
            <div class="sub">{{ $menor->nome }}</div>
        </div>
        <div class="card">
            <div class="label">Média mundial</div>
            <div class="valor">US$ {{ number_format($media, 0, ',', '.') }}</div>
            <div class="sub">{{ $paises->count() }} países</div>
        </div>
        <div class="card">
            <div class="label">Brasil</div>
            <div class="valor">US$ {{ number_format($brasil->pib_per_capita, 0, ',', '.') }}</div>
            <div class="sub">{{ $brasil->regiao }}</div>
        </div>
    </div>

    {{-- Tabela de países --}}
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>País</th>
                <th>Região</th>
                <th>Ano</th>
                <th>PIB per capita</th>
            </tr>
        </thead>
        <tbody>
            @foreach($paises as $index => $pais)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $pais->nome }}</td>
                <td>{{ $pais->regiao }}</td>
                <td>{{ $pais->ano }}</td>
                <td>US$ {{ number_format($pais->pib_per_capita, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>