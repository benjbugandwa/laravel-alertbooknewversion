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
        <x-ui-card>
            <div class="font-semibold">Évolution des alertes ({{ $this->days }} jours)</div>
            <div class="mt-3">
                <canvas id="chartEvolution" height="120"></canvas>
            </div>
        </x-ui-card>

        <x-ui-card>
            <div class="font-semibold">Alertes par statut</div>
            <div class="mt-3">
                <canvas id="chartStatus" height="120"></canvas>
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
    </div>

    {{-- Chart.js + Alpine helper --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    <script>
        function dashboardCharts(payload) {
            return {
                payload,
                charts: {
                    evolution: null,
                    status: null,
                    province: null,
                },

                init() {
                    this.buildAll(this.payload);

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
                },

                destroyAll() {
                    Object.values(this.charts).forEach(ch => {
                        if (ch) ch.destroy();
                    });
                    this.charts.evolution = this.charts.status = this.charts.province = null;
                },



                buildAll(p) {
                    const ctxEvo = document.getElementById('chartEvolution');
                    const ctxStatus = document.getElementById('chartStatus');
                    const ctxProv = document.getElementById('chartProvince');

                    if (!ctxEvo || !ctxStatus || !ctxProv) return;

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
                        backgroundColor: 'rgba(17, 24, 39, 0.92)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        padding: 10,
                        cornerRadius: 10,
                        displayColors: false,
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
                                            return `${ctx.label}: ${v} (${pct}%)`;
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
                }







            }
        }
    </script>
</div>
