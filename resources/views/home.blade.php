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

        {{-- Mapa mundi --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5 mb-6">
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm font-medium text-gray-500">Mapa mundial — PIB per capita 2022</p>
                <div class="flex items-center gap-3">
                    <span class="text-xs text-gray-400">Menor PIB</span>
                    <div style="width:120px;height:8px;border-radius:4px;background:linear-gradient(to right, #dbeafe, #1e40af)"></div>
                    <span class="text-xs text-gray-400">Maior PIB</span>
                </div>
            </div>
            <div id="mapa-container" style="width:100%;position:relative;">
                <svg id="mapa-mundo" style="width:100%;height:auto;display:block;"></svg>
                <div id="mapa-tooltip"
                     style="display:none;position:absolute;background:white;border:1px solid #e5e7eb;
                            border-radius:8px;padding:8px 12px;font-size:12px;pointer-events:none;
                            box-shadow:0 4px 6px -1px rgba(0,0,0,0.1);">
                </div>
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
    <script src="https://cdn.jsdelivr.net/npm/topojson-client@3/dist/topojson-client.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/d3@7"></script>
    <script>
        const dadosPaises = @json($paisesMap);
    
        const largura = document.getElementById('mapa-mundo').parentElement.offsetWidth;
        const altura  = largura * 0.5;
        document.getElementById('mapa-mundo').setAttribute('viewBox', `0 0 ${largura} ${altura}`);
    
        const svg = d3.select('#mapa-mundo');
        const g   = svg.append('g');
    
        const projecao = d3.geoNaturalEarth1()
            .scale(largura / 6.3)
            .translate([largura / 2, altura / 2]);
    
        const caminho = d3.geoPath().projection(projecao);
    
        const valoresPib = Object.values(dadosPaises).map(d => d.pib);
        const escala = d3.scaleSequentialLog()
            .domain([Math.min(...valoresPib), Math.max(...valoresPib)])
            .interpolator(d3.interpolateBlues);
    
        const tooltip = document.getElementById('mapa-tooltip');
    
        // tabela de conversão: numérico ISO 3166-1 → alfa-3
        // sem duplicatas, sem zeros à esquerda (que viram octais em JS)
        const tabelaCodigos = {
            4:'AFG', 8:'ALB', 12:'DZA', 20:'AND', 24:'AGO', 31:'AZE', 32:'ARG',
            36:'AUS', 40:'AUT', 50:'BGD', 51:'ARM', 56:'BEL', 64:'BTN', 68:'BOL',
            70:'BIH', 76:'BRA', 100:'BGR', 112:'BLR', 116:'KHM', 120:'CMR',
            124:'CAN', 144:'LKA', 152:'CHL', 156:'CHN', 170:'COL', 180:'COD',
            188:'CRI', 191:'HRV', 192:'CUB', 196:'CYP', 203:'CZE', 208:'DNK',
            214:'DOM', 218:'ECU', 222:'SLV', 231:'ETH', 233:'EST', 246:'FIN',
            250:'FRA', 266:'GAB', 268:'GEO', 276:'DEU', 288:'GHA', 300:'GRC',
            320:'GTM', 332:'HTI', 336:'VAT', 340:'HND', 348:'HUN', 352:'ISL',
            356:'IND', 360:'IDN', 364:'IRN', 368:'IRQ', 372:'IRL', 376:'ISR',
            380:'ITA', 388:'JAM', 392:'JPN', 398:'KAZ', 400:'JOR', 404:'KEN',
            408:'PRK', 410:'KOR', 414:'KWT', 417:'KGZ', 418:'LAO', 422:'LBN',
            428:'LVA', 430:'LBR', 434:'LBY', 440:'LTU', 442:'LUX', 454:'MWI',
            458:'MYS', 470:'MLT', 484:'MEX', 492:'MCO', 496:'MNG', 499:'MNE',
            504:'MAR', 508:'MOZ', 516:'NAM', 524:'NPL', 528:'NLD', 554:'NZL',
            558:'NIC', 562:'NER', 566:'NGA', 578:'NOR', 586:'PAK', 591:'PAN',
            600:'PRY', 604:'PER', 608:'PHL', 616:'POL', 620:'PRT', 630:'PRI',
            634:'QAT', 642:'ROU', 643:'RUS', 674:'SMR', 682:'SAU', 686:'SEN',
            688:'SRB', 694:'SLE', 703:'SVK', 704:'VNM', 705:'SVN', 706:'SOM',
            710:'ZAF', 716:'ZWE', 724:'ESP', 729:'SDN', 752:'SWE', 756:'CHE',
            760:'SYR', 762:'TJK', 764:'THA', 780:'TTO', 784:'ARE', 792:'TUR',
            795:'TKM', 800:'UGA', 804:'UKR', 807:'MKD', 818:'EGY', 826:'GBR',
            840:'USA', 858:'URY', 860:'UZB', 862:'VEN', 887:'YEM', 894:'ZMB',
        };
    
        function converterCodigo(idNumerico) {
            return tabelaCodigos[parseInt(idNumerico)] || null;
        }
    
        // busca o GeoJSON uma única vez
        d3.json('https://cdn.jsdelivr.net/npm/world-atlas@2/countries-110m.json')
            .then(function(mundo) {
                const paises = topojson.feature(mundo, mundo.objects.countries);
    
                g.selectAll('path')
                    .data(paises.features)
                    .enter()
                    .append('path')
                    .attr('d', caminho)
                    .attr('fill', function(d) {
                        const codigo = converterCodigo(d.id);
                        const dado   = dadosPaises[codigo];
                        return dado ? escala(dado.pib) : '#e5e7eb';
                    })
                    .attr('stroke', '#fff')
                    .attr('stroke-width', 0.4)
                    .style('cursor', 'pointer')
                    .on('mousemove', function(event, d) {
                        const codigo = converterCodigo(d.id);
                        const dado   = dadosPaises[codigo];
                        const [mx, my] = d3.pointer(event, document.getElementById('mapa-container'));
    
                        tooltip.style.display = 'block';
                        tooltip.style.left    = (mx + 12) + 'px';
                        tooltip.style.top     = (my - 40) + 'px';
    
                        if (dado) {
                            tooltip.innerHTML = `
                                <div style="font-weight:600;color:#111;margin-bottom:2px">${dado.nome}</div>
                                <div style="color:#6b7280">${dado.regiao}</div>
                                <div style="color:#2563eb;font-weight:600;margin-top:4px">
                                    US$ ${Number(dado.pib).toLocaleString('pt-BR', {minimumFractionDigits:0, maximumFractionDigits:0})}
                                </div>`;
                        } else {
                            tooltip.innerHTML = '<div style="color:#9ca3af">Sem dados</div>';
                        }
                    })
                    .on('mouseleave', function() {
                        tooltip.style.display = 'none';
                    });
            });
    </script>

</body>
</html>