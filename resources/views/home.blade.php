<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Dashboard PIB per capita</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: sans-serif; padding: 2rem; background: #f5f5f5; color: #333; }
        h1 { margin-bottom: 0.25rem; }
        .subtitulo { font-size: 13px; color: #888; margin-bottom: 1.5rem; }

        .cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
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

        .graficos {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .painel {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            border: 1px solid #e0e0e0;
        }
        .painel h2 { font-size: 14px; color: #555; margin-bottom: 1rem; }

        .filtros {
            display: flex;
            gap: 0.75rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
        }
        .filtros input, .filtros select {
            padding: 6px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 13px;
        }

        table { width: 100%; background: white; border-radius: 8px;
                border-collapse: collapse; border: 1px solid #e0e0e0; }
        th, td { padding: 10px 14px; text-align: left; border-bottom: 1px solid #f0f0f0; font-size: 14px; }
        th { font-size: 12px; color: #888; font-weight: 600; background: #fafafa; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #f9f9f9; }

        .badge {
            display: inline-block;
            font-size: 11px;
            padding: 2px 8px;
            border-radius: 10px;
            background: #e8f4fd;
            color: #1a6fa8;
        }
    </style>
</head>
<body>

    <h1>Dashboard PIB per capita mundial</h1>
    <p class="subtitulo">Fonte: World Bank API — ano 2022</p>

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

    {{-- Gráficos --}}
    <div class="graficos">
        <div class="painel">
            <h2>Top 10 países — PIB per capita (US$)</h2>
            <canvas id="graficoTop10" height="120"></canvas>
        </div>
        <div class="painel">
            <h2>Média por região</h2>
            <canvas id="graficoRegioes" height="120"></canvas>
        </div>
    </div>

    {{-- Tabela com filtro --}}
    <div class="painel">
        <h2>Todos os países</h2>
        <div class="filtros">
            <input type="text" id="busca" placeholder="Buscar país...">
            <select id="filtroRegiao">
                <option value="">Todas as regiões</option>
                @foreach($porRegiao as $r)
                    <option value="{{ $r->regiao }}">{{ $r->regiao }}</option>
                @endforeach
            </select>
        </div>
        <table id="tabelaPaises">
            <thead>
                <tr>
                    <th>#</th>
                    <th>País</th>
                    <th>Código</th>
                    <th>Região</th>
                    <th>PIB per capita</th>
                </tr>
            </thead>
            <tbody>
                @foreach($paises as $index => $pais)
                <tr data-nome="{{ strtolower($pais->nome) }}" data-regiao="{{ $pais->regiao }}">
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $pais->nome }}</td>
                    <td><span class="badge">{{ $pais->codigo }}</span></td>
                    <td>{{ $pais->regiao }}</td>
                    <td>US$ {{ number_format($pais->pib_per_capita, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // gráfico top 10
        const nomes  = @json($top10->pluck('nome'));
        const valores = @json($top10->pluck('pib_per_capita'));

        new Chart(document.getElementById('graficoTop10'), {
            type: 'bar',
            data: {
                labels: nomes,
                datasets: [{
                    data: valores,
                    backgroundColor: '#378ADD',
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        ticks: {
                            callback: v => 'US$ ' + v.toLocaleString('pt-BR')
                        }
                    }
                }
            }
        });

        // gráfico de regiões
        const regioes      = @json($porRegiao->pluck('regiao'));
        const mediasRegiao = @json($porRegiao->pluck('media_pib'));

        new Chart(document.getElementById('graficoRegioes'), {
            type: 'bar',
            data: {
                labels: regioes,
                datasets: [{
                    data: mediasRegiao,
                    backgroundColor: ['#185FA5','#1D9E75','#EF9F27','#D85A30','#7F77DD','#D4537E','#888780'],
                    borderRadius: 4,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    x: {
                        ticks: {
                            callback: v => 'US$ ' + v.toLocaleString('pt-BR')
                        }
                    }
                }
            }
        });

        // filtro da tabela — JavaScript puro, sem precisar recarregar a página
        const busca       = document.getElementById('busca');
        const filtroRegiao = document.getElementById('filtroRegiao');
        const linhas      = document.querySelectorAll('#tabelaPaises tbody tr');

        function filtrar() {
            const textoBusca  = busca.value.toLowerCase();
            const regiaoFiltro = filtroRegiao.value;

            linhas.forEach(linha => {
                const nome   = linha.dataset.nome;
                const regiao = linha.dataset.regiao;

                const passaBusca  = nome.includes(textoBusca);
                const passaRegiao = regiaoFiltro === '' || regiao === regiaoFiltro;

                linha.style.display = (passaBusca && passaRegiao) ? '' : 'none';
            });
        }

        busca.addEventListener('input', filtrar);
        filtroRegiao.addEventListener('change', filtrar);
    </script>

</body>
</html>