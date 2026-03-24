<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard PIB per capita</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-800 min-h-screen">

    {{-- Header --}}
    <header class="bg-white border-b border-gray-200 px-8 py-4 mb-6">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">Dashboard PIB per capita mundial</h1>
                <p class="text-sm text-gray-400 mt-0.5">Fonte: World Bank API — ano 2022</p>
            </div>
            <span class="text-sm bg-blue-50 text-blue-700 px-3 py-1 rounded-full">
                {{ $paises->count() }} países
            </span>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-8 pb-12">

        {{-- Cards de métricas --}}
        <div class="grid grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <p class="text-xs text-gray-400 mb-1">Maior PIB per capita</p>
                <p class="text-2xl font-semibold">US$ {{ number_format($maior->pib_per_capita, 0, ',', '.') }}</p>
                <p class="text-sm text-gray-500 mt-1">{{ $maior->nome }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <p class="text-xs text-gray-400 mb-1">Menor PIB per capita</p>
                <p class="text-2xl font-semibold">US$ {{ number_format($menor->pib_per_capita, 0, ',', '.') }}</p>
                <p class="text-sm text-gray-500 mt-1">{{ $menor->nome }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <p class="text-xs text-gray-400 mb-1">Média mundial</p>
                <p class="text-2xl font-semibold">US$ {{ number_format($media, 0, ',', '.') }}</p>
                <p class="text-sm text-gray-500 mt-1">{{ $paises->count() }} países</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <p class="text-xs text-gray-400 mb-1">Brasil</p>
                <p class="text-2xl font-semibold">US$ {{ number_format($brasil->pib_per_capita, 0, ',', '.') }}</p>
                <p class="text-sm text-gray-500 mt-1">{{ $brasil->regiao }}</p>
            </div>
        </div>

        {{-- Gráficos --}}
        <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="col-span-2 bg-white rounded-xl border border-gray-200 p-5">
                <p class="text-sm font-medium text-gray-500 mb-4">Top 10 países — PIB per capita (US$)</p>
                <canvas id="graficoTop10" height="100"></canvas>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <p class="text-sm font-medium text-gray-500 mb-4">Média por região</p>
                <canvas id="graficoRegioes" height="200"></canvas>
            </div>
        </div>

        {{-- Tabela --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm font-medium text-gray-500">Todos os países</p>
                <div class="flex gap-2">
                    <input
                        type="text"
                        id="busca"
                        placeholder="Buscar país..."
                        class="text-sm border border-gray-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-100"
                    >
                    <select
                        id="filtroRegiao"
                        class="text-sm border border-gray-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-100"
                    >
                        <option value="">Todas as regiões</option>
                        @foreach($porRegiao as $r)
                            <option value="{{ $r->regiao }}">{{ $r->regiao }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <table class="w-full" id="tabelaPaises">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left text-xs text-gray-400 font-medium pb-2 w-12">#</th>
                        <th class="text-left text-xs text-gray-400 font-medium pb-2">País</th>
                        <th class="text-left text-xs text-gray-400 font-medium pb-2">Código</th>
                        <th class="text-left text-xs text-gray-400 font-medium pb-2">Região</th>
                        <th class="text-right text-xs text-gray-400 font-medium pb-2">PIB per capita</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($paises as $index => $pais)
                    <tr
                        class="border-b border-gray-50 hover:bg-gray-50 transition-colors"
                        data-nome="{{ strtolower($pais->nome) }}"
                        data-regiao="{{ $pais->regiao }}"
                    >
                        <td class="py-2.5 text-sm text-gray-400">{{ $index + 1 }}</td>
                        <td class="py-2.5 text-sm font-medium">{{ $pais->nome }}</td>
                        <td class="py-2.5">
                            <span class="text-xs bg-blue-50 text-blue-600 px-2 py-0.5 rounded font-mono">
                                {{ $pais->codigo }}
                            </span>
                        </td>
                        <td class="py-2.5 text-sm text-gray-500">{{ $pais->regiao }}</td>
                        <td class="py-2.5 text-sm text-right font-medium">
                            US$ {{ number_format($pais->pib_per_capita, 0, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const nomes   = @json($top10->pluck('nome'));
        const valores = @json($top10->pluck('pib_per_capita'));

        new Chart(document.getElementById('graficoTop10'), {
            type: 'bar',
            data: {
                labels: nomes,
                datasets: [{
                    data: valores,
                    backgroundColor: '#3B82F6',
                    borderRadius: 6,
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

        const regioes      = @json($porRegiao->pluck('regiao'));
        const mediasRegiao = @json($porRegiao->pluck('media_pib'));

        new Chart(document.getElementById('graficoRegioes'), {
            type: 'bar',
            data: {
                labels: regioes,
                datasets: [{
                    data: mediasRegiao,
                    backgroundColor: ['#3B82F6','#10B981','#F59E0B','#EF4444','#8B5CF6','#EC4899','#6B7280'],
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

        const busca        = document.getElementById('busca');
        const filtroRegiao = document.getElementById('filtroRegiao');
        const linhas       = document.querySelectorAll('#tabelaPaises tbody tr');

        function filtrar() {
            const textoBusca   = busca.value.toLowerCase();
            const regiaoFiltro = filtroRegiao.value;
            linhas.forEach(linha => {
                const passaBusca  = linha.dataset.nome.includes(textoBusca);
                const passaRegiao = regiaoFiltro === '' || linha.dataset.regiao === regiaoFiltro;
                linha.style.display = (passaBusca && passaRegiao) ? '' : 'none';
            });
        }

        busca.addEventListener('input', filtrar);
        filtroRegiao.addEventListener('change', filtrar);
    </script>

</body>
</html>