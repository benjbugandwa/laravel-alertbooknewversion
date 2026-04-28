<div>
    {{-- Spinner de chargement --}}
    <div wire:loading class="fixed inset-0 z-50 flex items-center justify-center bg-white/60 backdrop-blur-sm">
        <div class="flex flex-col items-center bg-white p-6 rounded-xl shadow-xl">
            <svg class="h-10 w-10 animate-spin text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="mt-4 text-sm font-semibold text-gray-700">Actualisation des données...</span>
        </div>
    </div>

    <div class="space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <div class="text-2xl font-bold">Dashboard</div>
            <div class="text-sm text-gray-600">
                @if ($chart['scope']['isSuper'])
                    Vue globale (toutes provinces).
                @else
                    Vue province :
                    <b>{{ $chart['scope']['nom_province'] ?? ($chart['scope']['code_province'] ?? '-') }}</b>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-2">
            @if ($chart['scope']['isSuper'])
                <select wire:model.live="selectedProvince" class="text-sm border-gray-300 rounded-md py-1 pr-8">
                    <option value="">Toutes les provinces</option>
                    @foreach($provinces as $prov)
                        <option value="{{ $prov->code_province }}">{{ $prov->nom_province }}</option>
                    @endforeach
                </select>
                @if($selectedProvince)
                    <select wire:model.live="selectedTerritoire" class="text-sm border-gray-300 rounded-md py-1 pr-8">
                        <option value="">Tous les territoires</option>
                        @foreach($territoires as $terr)
                            <option value="{{ $terr->code_territoire }}">{{ $terr->nom_territoire }}</option>
                        @endforeach
                    </select>
                @endif
            @endif
            <x-ui-button size="sm" variant="secondary" wire:click="setDays(30)">30j</x-ui-button>
            <x-ui-button size="sm" variant="secondary" wire:click="setDays(90)">90j</x-ui-button>
            <x-ui-button size="sm" variant="secondary" wire:click="setDays(180)">180j</x-ui-button>
        </div>
    </div>

    {{-- KPI cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-ui-card>
            <div class="text-sm text-gray-600">Utilisateurs actifs</div>
            <div class="mt-2 text-3xl font-bold">{{ $chart['users']['active'] }}</div>
        </x-ui-card>

        <x-ui-card>
            <div class="text-sm text-gray-600">En attente d’activation</div>
            <div class="mt-2 text-3xl font-bold">{{ $chart['users']['pending'] }}</div>
            <div class="mt-2 text-xs text-gray-500">
                (is_active = false)
            </div>
        </x-ui-card>

        <x-ui-card>
            <div class="text-sm text-gray-600">Alertes (période)</div>
            <div class="mt-2 text-3xl font-bold">
                {{ collect($chart['evolution']['data'])->sum() }}
            </div>
            <div class="mt-2 text-xs text-gray-500">
                Derniers {{ $this->days }} jours (date_incident)
            </div>
        </x-ui-card>

        <x-ui-card>
            <div class="text-sm text-gray-600">Statuts (catégories)</div>
            <div class="mt-2 text-3xl font-bold">
                {{ count($chart['byStatus']['labels']) }}
            </div>
        </x-ui-card>
    </div>

    {{-- Charts --}}
    <div x-data="dashboardCharts(@js($chart))" x-init="init()"
        x-on:livewire:navigated.window="rebuild(@js($chart))"
        x-on:chart-rebuild.window="rebuild(@js($chart))" class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <x-ui-card class="lg:col-span-2">
            <div class="font-semibold">Évolution des alertes ({{ $this->days }} jours)</div>
            <div class="mt-3">
                <canvas id="chartEvolution" height="80"></canvas>
            </div>
        </x-ui-card>

        <x-ui-card>
            <div class="font-semibold">Alertes par statut</div>
            <div class="mt-3">
                <canvas id="chartStatus" height="200"></canvas>
            </div>
        </x-ui-card>

        <x-ui-card>
            <div class="font-semibold">Alertes par type d'événement</div>
            <div class="mt-3">
                <canvas id="chartEventType" height="200"></canvas>
            </div>
        </x-ui-card>

        <x-ui-card class="lg:col-span-2">
            <div class="font-semibold">Alertes par province</div>
            <div class="mt-3">
                <canvas id="chartProvince" height="90">

                </canvas>
            </div>

            {{-- Tableau Incidents par province --}}
            <div class="mt-4 overflow-hidden rounded-xl border border-gray-200">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-4 py-2 text-left font-medium">Province</th>
                            <th class="px-4 py-2 text-right font-medium">Total</th>
                            <th class="px-4 py-2 text-right font-medium">%</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                        @forelse(($chart['byProvince']['table'] ?? []) as $row)
                            <tr class="hover:bg-gray-50/60">
                                <td class="px-4 py-2">
                                    <div class="font-medium text-gray-900">{{ $row['label'] }}</div>
                                </td>
                                <td class="px-4 py-2 text-right font-semibold text-gray-900">
                                    {{ $row['total'] }}
                                </td>
                                <td class="px-4 py-2 text-right text-gray-700">
                                    {{ number_format($row['pct'], 1) }}%
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-4 py-3 text-gray-600" colspan="3">Aucune donnée.</td>
                            </tr>
                        @endforelse
                    </tbody>

                    @if (!empty($chart['byProvince']['table']))
                        <tfoot class="bg-gray-50 text-gray-700">
                            <tr>
                                <td class="px-4 py-2 font-medium">Total</td>
                                <td class="px-4 py-2 text-right font-semibold">{{ $chart['byProvince']['sum'] ?? 0 }}
                                </td>
                                <td class="px-4 py-2 text-right font-medium">100%</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>


            <div class="mt-2 text-xs text-gray-500">
                Top 15 provinces (ou votre province si vous n’êtes pas superadmin).
            </div>
        </x-ui-card>

        {{-- Carte Territoires --}}
        <x-ui-card class="lg:col-span-2">
            <div class="font-semibold">Carte des incidents par Territoire</div>
            <div class="mt-3 relative z-0 border border-gray-200 rounded-xl overflow-hidden" wire:ignore>
                <div id="mapTerritoires" style="height: 500px; width: 100%; z-index: 1;"></div>
            </div>
        </x-ui-card>
    </div>

    {{-- Chart.js, Leaflet + Alpine helper --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    <script>
        function dashboardCharts(payload) {
            return {
                payload,
                charts: {
                    evolution: null,
                    status: null,
                    eventType: null,
                    province: null,
                },
                map: null,
                geoJsonLayer: null,
                geoJsonData: null,

                init() {
                    this.buildAll(this.payload);
                    this.initMap();

                    // Quand Livewire re-render le composant (après clic sur 30j/90j/180j)
                    document.addEventListener('livewire:initialized', () => {
                        Livewire.hook('message.processed', (message, component) => {
                            // Reconstruit après render
                            window.dispatchEvent(new CustomEvent('chart-rebuild'));
                        });
                    });
                },

                rebuild(newPayload) {
                    this.payload = newPayload;
                    this.destroyAll();
                    this.buildAll(this.payload);
                    if (this.geoJsonData) {
                        this.renderGeoJson();
                    }
                },

                destroyAll() {
                    Object.values(this.charts).forEach(ch => {
                        if (ch) ch.destroy();
                    });
                    this.charts.evolution = this.charts.status = this.charts.eventType = this.charts.province = null;
                },



                buildAll(p) {
                    const ctxEvo = document.getElementById('chartEvolution');
                    const ctxStatus = document.getElementById('chartStatus');
                    const ctxEventType = document.getElementById('chartEventType');
                    const ctxProv = document.getElementById('chartProvince');

                    if (!ctxEvo || !ctxStatus || !ctxEventType || !ctxProv) return;

                    // --- Style global (grilles claires + textes doux) ---
                    const gridColor = 'rgba(17, 24, 39, 0.08)'; // gris très léger
                    const tickColor = 'rgba(17, 24, 39, 0.55)'; // gris doux
                    const borderColor = 'rgba(17, 24, 39, 0.10)';

                    // Palette douce (pas piquante)
                    const palette = [
                        '#2563EB', // bleu
                        '#10B981', // vert
                        '#F59E0B', // ambre
                        '#EF4444', // rouge doux
                        '#8B5CF6', // violet
                        '#06B6D4', // cyan
                        '#64748B', // slate
                        '#84CC16', // lime doux
                    ];

                    const commonScales = {
                        x: {
                            grid: {
                                color: gridColor
                            },
                            ticks: {
                                color: tickColor,
                                maxTicksLimit: 8
                            },
                            border: {
                                color: borderColor
                            },
                        },
                        y: {
                            grid: {
                                color: gridColor
                            },
                            ticks: {
                                color: tickColor,
                                precision: 0
                            },
                            border: {
                                color: borderColor
                            },
                            beginAtZero: true,
                        },
                    };

                    const commonTooltip = {
                        enabled: true,
                        backgroundColor: 'rgba(17, 24, 39, 0.95)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        padding: 12,
                        cornerRadius: 6,
                        displayColors: true,
                    };

                    // Evolution (line)
                    this.charts.evolution = new Chart(ctxEvo, {
                        type: 'line',
                        data: {
                            labels: p.evolution.labels,
                            datasets: [{
                                label: 'Incidents',
                                data: p.evolution.data,
                                borderColor: palette[0],
                                backgroundColor: 'rgba(37, 99, 235, 0.12)',
                                fill: true,
                                tension: 0.25,
                                pointRadius: 2,
                                pointHoverRadius: 5,
                            }],
                        },
                        options: {
                            responsive: true,
                            interaction: {
                                mode: 'index',
                                intersect: false
                            },
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: commonTooltip,
                                // Afficher la valeur sur les points (discret)
                                datalabels: {
                                    color: tickColor,
                                    align: 'top',
                                    anchor: 'end',
                                    formatter: (v) => (v ? v : ''),
                                    font: {
                                        size: 10,
                                        weight: '600'
                                    },
                                }
                            },
                            scales: commonScales,
                        },
                        plugins: [ChartDataLabels],
                    });

                    // Status (doughnut) — afficher % + tooltip
                    const statusTotal = (p.byStatus.data || []).reduce((a, b) => a + b, 0);

                    this.charts.status = new Chart(ctxStatus, {
                        type: 'doughnut',
                        data: {
                            labels: p.byStatus.labels,
                            datasets: [{
                                data: p.byStatus.data,
                                backgroundColor: p.byStatus.labels.map((_, i) => palette[i % palette
                                    .length]),
                                borderColor: '#fff',
                                borderWidth: 2,
                                hoverOffset: 6,
                                cutout: '68%', // donut comme ton exemple
                            }],
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        color: tickColor,
                                        boxWidth: 10,
                                        boxHeight: 10,
                                        usePointStyle: true
                                    }
                                },
                                tooltip: {
                                    ...commonTooltip,
                                    callbacks: {
                                        label: (ctx) => {
                                            const v = ctx.raw || 0;
                                            const pct = statusTotal ? Math.round((v / statusTotal) * 100) : 0;
                                            return ` ${ctx.label}: ${v} (${pct}%)`;
                                        }
                                    }
                                },
                                datalabels: {
                                    color: '#fff',
                                    font: {
                                        weight: '700',
                                        size: 11
                                    },
                                    formatter: (value) => {
                                        if (!statusTotal) return '';
                                        const pct = Math.round((value / statusTotal) * 100);
                                        return pct >= 6 ? `${pct}%` :
                                            ''; // n’affiche pas les petits % (lisibilité)
                                    },
                                }
                            },
                        },
                        plugins: [ChartDataLabels],
                    });

                    // EventType (bar horizontal)
                    this.charts.eventType = new Chart(ctxEventType, {
                        type: 'bar',
                        data: {
                            labels: p.byEventType.labels,
                            datasets: [{
                                label: 'Incidents',
                                data: p.byEventType.data,
                                backgroundColor: 'rgba(239, 68, 68, 0.85)', // rouge doux
                                borderRadius: 4,
                                maxBarThickness: 24,
                            }],
                        },
                        options: {
                            responsive: true,
                            indexAxis: 'y', // Barres horizontales pour lire les textes longs
                            interaction: {
                                mode: 'index',
                                intersect: false
                            },
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    ...commonTooltip,
                                    callbacks: {
                                        label: (ctx) => ` Incidents: ${ctx.raw ?? 0}`
                                    }
                                },
                                datalabels: {
                                    color: tickColor,
                                    anchor: 'end',
                                    align: 'end',
                                    offset: 4,
                                    font: { size: 10, weight: '700' },
                                    formatter: (v) => (v ? v : ''),
                                }
                            },
                            scales: {
                                x: {
                                    grid: { color: gridColor },
                                    ticks: { color: tickColor, precision: 0 },
                                    border: { color: borderColor },
                                    beginAtZero: true,
                                },
                                y: {
                                    grid: { display: false },
                                    ticks: { color: tickColor, font: { size: 10 } },
                                    border: { color: borderColor },
                                }
                            },
                        },
                        plugins: [ChartDataLabels],
                    });

                    // Province (bar) — valeurs au-dessus des barres + tooltip
                    this.charts.province = new Chart(ctxProv, {
                        type: 'bar',
                        data: {
                            labels: p.byProvince.labels,
                            datasets: [{
                                label: 'Incidents',
                                data: p.byProvince.data,
                                backgroundColor: 'rgba(37, 99, 235, 0.85)',
                                borderRadius: 10,
                                maxBarThickness: 38,
                            }],
                        },
                        options: {
                            responsive: true,
                            interaction: {
                                mode: 'index',
                                intersect: false
                            },
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    ...commonTooltip,
                                    callbacks: {
                                        label: (ctx) => `Incidents: ${ctx.raw ?? 0}`
                                    }
                                },
                                datalabels: {
                                    color: tickColor,
                                    anchor: 'end',
                                    align: 'end',
                                    offset: 2,
                                    font: {
                                        size: 10,
                                        weight: '700'
                                    },
                                    formatter: (v) => (v ? v : ''),
                                }
                            },
                            scales: {
                                x: {
                                    grid: {
                                        display: false
                                    }, // comme l’exemple (bar chart sans grille verticale)
                                    ticks: {
                                        color: tickColor
                                    },
                                    border: {
                                        color: borderColor
                                    },
                                },
                                y: {
                                    grid: {
                                        color: gridColor
                                    },
                                    ticks: {
                                        color: tickColor,
                                        precision: 0
                                    },
                                    border: {
                                        color: borderColor
                                    },
                                    beginAtZero: true,
                                }
                            },
                        },
                        plugins: [ChartDataLabels],
                    });
                },

                initMap() {
                    const mapContainer = document.getElementById('mapTerritoires');
                    if (!mapContainer) return;
                    
                    // Centre RDC
                    this.map = L.map('mapTerritoires').setView([-4.0383, 21.7587], 5);

                    // Fond de carte clair avec transparence
                    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a>',
                        subdomains: 'abcd',
                        maxZoom: 20,
                        opacity: 0.6
                    }).addTo(this.map);

                    // Chargement du GeoJSON
                    fetch('/cod_admin2_em.geojson')
                        .then(res => res.json())
                        .then(data => {
                            this.geoJsonData = data;
                            this.renderGeoJson();
                        });
                },

                getColor(d) {
                    // Dégradé de rouges
                    return d > 100 ? '#67000d' :
                           d > 50  ? '#a50f15' :
                           d > 20  ? '#cb181d' :
                           d > 10  ? '#ef3b2c' :
                           d > 5   ? '#fb6a4a' :
                           d > 0   ? '#fc9272' :
                                     'rgba(240,240,240,0.5)'; // transparent/gris clair
                },

                renderGeoJson() {
                    if (!this.map || !this.geoJsonData) return;
                    if (this.geoJsonLayer) {
                        this.map.removeLayer(this.geoJsonLayer);
                    }

                    const territoryData = this.payload.byTerritoire || {};

                    // Filtrer par province si non superadmin
                    let filteredFeatures = this.geoJsonData.features;
                    if (!this.payload.scope.isSuper && this.payload.scope.code_province) {
                        filteredFeatures = filteredFeatures.filter(f => 
                            f.properties.adm1_pcode === this.payload.scope.code_province
                        );
                    }

                    const geoDataToRender = {
                        ...this.geoJsonData,
                        features: filteredFeatures
                    };

                    this.geoJsonLayer = L.geoJSON(geoDataToRender, {
                        style: (feature) => {
                            let name = feature.properties.adm2_name ? feature.properties.adm2_name.toLowerCase().trim() : '';
                            let count = territoryData[name] || 0;
                            return {
                                fillColor: this.getColor(count),
                                weight: 2, // Contour renforcé
                                opacity: 1,
                                color: '#333', // Bordures foncées pour bien faire ressortir les territoires
                                dashArray: '',
                                fillOpacity: count > 0 ? 0.9 : 0.3
                            };
                        },
                        onEachFeature: (feature, layer) => {
                            let name = feature.properties.adm2_name || 'Inconnu';
                            let count = territoryData[name.toLowerCase().trim()] || 0;
                            
                            let tooltipContent = `<div class="text-sm" style="font-family: inherit;"><div class="font-bold text-gray-800">${name}</div><div class="text-gray-600">Incidents: <span class="font-semibold text-gray-900">${count}</span></div></div>`;
                            layer.bindTooltip(tooltipContent, {
                                sticky: true,
                                direction: 'top',
                                className: 'bg-white border shadow p-2'
                            });

                            layer.on({
                                mouseover: (e) => {
                                    let l = e.target;
                                    l.setStyle({
                                        weight: 3,
                                        color: '#000',
                                        dashArray: '',
                                        fillOpacity: 1
                                    });
                                    if (!L.Browser.ie && !L.Browser.opera && !L.Browser.edge) {
                                        l.bringToFront();
                                    }
                                },
                                mouseout: (e) => {
                                    this.geoJsonLayer.resetStyle(e.target);
                                }
                            });
                        }
                    }).addTo(this.map);

                    // Ajuster la vue si on n'est pas superadmin et qu'il y a des données
                    if (!this.payload.scope.isSuper && filteredFeatures.length > 0) {
                        this.map.fitBounds(this.geoJsonLayer.getBounds());
                    } else if (this.payload.scope.isSuper) {
                        // Remettre au centre par défaut
                        this.map.setView([-4.0383, 21.7587], 5);
                    }
                }

            }
        }
    </script>
</div>
</div>
