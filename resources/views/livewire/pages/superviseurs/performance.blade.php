<div class="space-y-6" x-data="supervisorPerfChart(@js($trend))" x-on:supervisor-performance-updated.window="update($event.detail.trend)">

    <div class="flex items-start justify-between gap-4">
        <div>
            <div class="text-2xl font-bold">Performance superviseur</div>
            <div class="text-sm text-gray-600">
                Période d’analyse : <span class="font-medium">3 derniers mois</span> (modifiable).
            </div>
        </div>
    </div>

    <x-ui-card>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="space-y-1">
                <label class="text-sm font-medium text-gray-700">Superviseur</label>
                <select wire:model.live="superviseurId"
                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white">
                    @foreach ($this->superviseurs as $s)
                        <option value="{{ $s['id'] }}">
                            {{ $s['name'] }} — {{ $s['email'] }}
                            @if (auth()->user()->user_role === 'superadmin')
                                ({{ $s['code_province'] }})
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1">
                <label class="text-sm font-medium text-gray-700">Du</label>
                <input type="date" wire:model.live="rangeStart"
                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white">
            </div>

            <div class="space-y-1">
                <label class="text-sm font-medium text-gray-700">Au</label>
                <input type="date" wire:model.live="rangeEnd" max="{{ now()->toDateString() }}"
                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white">
            </div>
        </div>
    </x-ui-card>

    {{-- KPIs --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <x-ui-card>
            <div class="text-xs text-gray-500">Incidents assignés</div>
            <div class="text-2xl font-bold mt-1">{{ $stats['assigned_total'] }}</div>
            <div class="text-xs text-gray-500 mt-2">Sur la période</div>
        </x-ui-card>

        <x-ui-card>
            <div class="text-xs text-gray-500">Validés par lui</div>
            <div class="text-2xl font-bold mt-1">{{ $stats['validated_total'] }}</div>
            <div class="text-xs text-gray-500 mt-2">Parmi les assignés</div>
        </x-ui-card>

        <x-ui-card>
            <div class="text-xs text-gray-500">En attente</div>
            <div class="text-2xl font-bold mt-1">{{ $stats['pending_total'] }}</div>
            <div class="text-xs text-gray-500 mt-2">Assignés non validés</div>
        </x-ui-card>

        <x-ui-card>
            <div class="text-xs text-gray-500">Taux de validation</div>
            <div class="text-2xl font-bold mt-1">{{ $stats['validation_rate'] }}%</div>

            <div class="mt-3">
                <div class="h-2 rounded bg-gray-100 overflow-hidden">
                    <div class="h-2 bg-[#0B4EA2]" style="width: {{ $stats['validation_rate'] }}%"></div>
                </div>
                <div class="text-xs text-gray-500 mt-1">
                    {{ $stats['validated_total'] }} / {{ $stats['assigned_total'] }}
                </div>
            </div>
        </x-ui-card>
    </div>

    {{-- Trend chart --}}
    <x-ui-card>
        <div class="flex items-center justify-between">
            <div>
                <div class="text-sm font-semibold">Tendance validations (12 semaines)</div>
                <div class="text-xs text-gray-500">Nombre de validations par semaine</div>
            </div>
        </div>

        <div class="mt-4">
            <canvas x-ref="chart" height="90"></canvas>
        </div>
    </x-ui-card>

    {{-- Notes + Referencements --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <x-ui-card>
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm font-semibold">Notes effectuées</div>
                    <div class="text-xs text-gray-500">Total période : {{ $stats['notes_total'] }}</div>
                </div>
            </div>

            <div class="mt-4 space-y-3">
                @forelse($recentNotes as $n)
                    <div class="border rounded-xl p-3">
                        <div class="flex items-center justify-between gap-2">
                            <div class="text-xs text-gray-500">
                                {{ \Carbon\Carbon::parse($n['created_at'])->format('Y-m-d H:i') }}
                                • {{ $n['code_incident'] ?? '—' }}
                            </div>

                            @if ($n['is_confidential'])
                                <span
                                    class="text-xs px-2 py-1 rounded-full border bg-yellow-50 text-yellow-800 border-yellow-200">
                                    Confidentiel
                                </span>
                            @endif
                        </div>
                        <div class="text-sm text-gray-800 mt-2 whitespace-pre-line">
                            {{ \Illuminate\Support\Str::limit($n['case_note'], 220) }}
                        </div>
                    </div>
                @empty
                    <div class="text-sm text-gray-500">Aucune note sur la période.</div>
                @endforelse
            </div>
        </x-ui-card>

        <x-ui-card>
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm font-semibold">Référencements effectués</div>
                    <div class="text-xs text-gray-500">Total période : {{ $stats['refs_total'] }}</div>
                </div>
            </div>

            <div class="mt-4 space-y-3">
                @forelse($recentRefs as $r)
                    <div class="border rounded-xl p-3">
                        <div class="flex items-center justify-between gap-2">
                            <div class="text-xs text-gray-500">
                                {{ \Carbon\Carbon::parse($r['created_at'])->format('Y-m-d H:i') }}
                                • {{ $r['code_incident'] ?? '—' }}
                            </div>

                            <span
                                class="text-xs px-2 py-1 rounded-full border bg-gray-50 text-gray-700 border-gray-200">
                                {{ $r['statut_reponse'] ?? '—' }}
                            </span>
                        </div>

                        <div class="mt-2 text-sm">
                            <div class="font-medium text-gray-900">
                                {{ $r['code_referencement'] ?? '—' }}
                            </div>
                            <div class="text-gray-700">
                                {{ $r['provider_name'] ?? '—' }}
                                @if (!empty($r['focalpoint_number']))
                                    <span class="text-gray-500">• FP: {{ $r['focalpoint_number'] }}</span>
                                @endif
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                Type: {{ $r['type_reponse'] ?? '—' }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-sm text-gray-500">Aucun référencement sur la période.</div>
                @endforelse
            </div>
        </x-ui-card>
    </div>

    {{-- Chart.js --}}
    @once
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            function supervisorPerfChart(initialTrend) {
                return {
                    chart: null,
                    trend: initialTrend || {
                        labels: [],
                        values: []
                    },

                    init() {
                        const ctx = this.$refs.chart.getContext('2d');

                        this.chart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: this.trend.labels,
                                datasets: [{
                                    label: 'Validations',
                                    data: this.trend.values,
                                    tension: 0.35,
                                    borderWidth: 2,
                                    pointRadius: 3,
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    tooltip: {
                                        enabled: true
                                    },
                                    legend: {
                                        display: false
                                    },
                                },
                                scales: {
                                    x: {
                                        grid: {
                                            color: 'rgba(0,0,0,0.06)'
                                        },
                                        ticks: {
                                            maxTicksLimit: 12
                                        }
                                    },
                                    y: {
                                        beginAtZero: true,
                                        grid: {
                                            color: 'rgba(0,0,0,0.06)'
                                        },
                                        ticks: {
                                            precision: 0
                                        }
                                    }
                                }
                            }
                        });
                    },

                    update(trend) {
                        if (!this.chart) return;
                        this.chart.data.labels = trend.labels || [];
                        this.chart.data.datasets[0].data = trend.values || [];
                        this.chart.update();
                    }
                }
            }
        </script>
    @endonce
</div>
