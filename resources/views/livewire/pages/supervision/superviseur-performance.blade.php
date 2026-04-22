<div class="space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <div class="text-2xl font-bold">Performance des superviseurs</div>
            <div class="text-sm text-gray-600">
                Suivi des validations, notes et référencements par superviseur.
            </div>
        </div>
    </div>

    <x-ui-card>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            {{-- Province (superadmin uniquement) --}}
            @if (auth()->user()->user_role === 'superadmin')
                <div class="space-y-1">
                    <label class="text-sm font-medium text-gray-700">Province</label>
                    <select wire:model.live="province"
                        class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white">
                        <option value="">Toutes</option>
                        @foreach ($this->provinces as $p)
                            <option value="{{ $p['code'] }}">{{ $p['name'] }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="space-y-1 md:col-span-2">
                <label class="text-sm font-medium text-gray-700">Superviseur</label>
                <select wire:model.live="superviseurId"
                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white">
                    <option value="">-- Sélectionner --</option>
                    @foreach ($this->superviseurs as $s)
                        <option value="{{ $s['id'] }}">
                            {{ $s['name'] }} — {{ $s['email'] }}
                        </option>
                    @endforeach
                </select>
                @if ($selectedSuperviseur)
                    <div class="text-xs text-gray-500 mt-1">
                        Province: <span class="font-medium">{{ $selectedSuperviseur['code_province'] ?? '-' }}</span>
                    </div>
                @endif
            </div>

            <div class="space-y-1">
                <label class="text-sm font-medium text-gray-700">Période (optionnel)</label>
                <div class="grid grid-cols-2 gap-2">
                    <input type="date" wire:model.live="dateFrom"
                        class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white">
                    <input type="date" wire:model.live="dateTo"
                        class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white">
                </div>
                <div class="text-xs text-gray-500">Filtre sur la date de l’incident.</div>
            </div>
        </div>
    </x-ui-card>

    {{-- KPIs --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <x-ui-card>
            <div class="text-sm text-gray-600">Incidents assignés</div>
            <div class="text-3xl font-semibold mt-1">{{ $kpiAssigned }}</div>
            <div class="text-xs text-gray-500 mt-2">Hors archivés</div>
        </x-ui-card>

        <x-ui-card>
            <div class="text-sm text-gray-600">Validés</div>
            <div class="text-3xl font-semibold mt-1">{{ $kpiValidated }}</div>

            <div class="mt-3">
                <div class="flex items-center justify-between text-xs text-gray-600 mb-1">
                    <span>Taux de validation</span>
                    <span class="font-medium">{{ $pctValidated }}%</span>
                </div>
                <div class="h-2 rounded-full bg-gray-100 overflow-hidden">
                    <div class="h-2 bg-sky-700" style="width: {{ $pctValidated }}%"></div>
                </div>
            </div>
        </x-ui-card>

        <x-ui-card>
            <div class="text-sm text-gray-600">En attente</div>
            <div class="text-3xl font-semibold mt-1">{{ $kpiPending }}</div>
            <div class="text-xs text-gray-500 mt-2">Assignés mais non validés</div>
        </x-ui-card>
    </div>

    {{-- Activité --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <x-ui-card>
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm font-semibold">Notes récentes</div>
                    <div class="text-xs text-gray-500">Les 10 dernières notes ajoutées</div>
                </div>
            </div>

            <div class="mt-4 space-y-3">
                @if (empty($recentNotes))
                    <div class="text-sm text-gray-500">Aucune note trouvée.</div>
                @else
                    @foreach ($recentNotes as $n)
                        <div class="border rounded-xl p-3">
                            <div class="flex items-center justify-between gap-3">
                                <div class="text-sm font-medium">
                                    {{ $n['code_incident'] ?? 'Incident' }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ \Illuminate\Support\Carbon::parse($n['created_at'])->format('Y-m-d H:i') }}
                                </div>
                            </div>

                            <div class="text-sm text-gray-700 mt-2">
                                {{ $n['excerpt'] }}@if (strlen($n['excerpt'] ?? '') >= 160)
                                    …
                                @endif
                            </div>

                            <div class="mt-2 flex items-center justify-between">
                                @if ($n['is_confidential'])
                                    <span
                                        class="text-xs inline-flex px-2 py-1 rounded-full bg-red-50 text-red-700 border border-red-200">
                                        Confidentiel
                                    </span>
                                @else
                                    <span
                                        class="text-xs inline-flex px-2 py-1 rounded-full bg-gray-50 text-gray-700 border border-gray-200">
                                        Standard
                                    </span>
                                @endif

                                <a href="{{ route('incidents.show', $n['id_incident']) }}"
                                    class="text-sm text-sky-800 hover:underline">
                                    Voir incident →
                                </a>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </x-ui-card>

        <x-ui-card>
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm font-semibold">Référencements récents</div>
                    <div class="text-xs text-gray-500">Les 10 derniers référencements</div>
                </div>
            </div>

            <div class="mt-4 space-y-3">
                @if (empty($recentReferencements))
                    <div class="text-sm text-gray-500">Aucun référencement trouvé.</div>
                @else
                    @foreach ($recentReferencements as $r)
                        @php
                            $st = $r['statut_reponse'] ?? 'En attente';
                            $badge = match ($st) {
                                'En attente' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                'En cours' => 'bg-blue-100 text-blue-800 border-blue-200',
                                'Fournie' => 'bg-green-100 text-green-800 border-green-200',
                                'refusée' => 'bg-red-100 text-red-800 border-red-200',
                                default => 'bg-gray-100 text-gray-700 border-gray-200',
                            };
                        @endphp

                        <div class="border rounded-xl p-3">
                            <div class="flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="text-sm font-medium">
                                        {{ $r['code_referencement'] ?? '-' }}
                                        <span
                                            class="text-xs ml-2 inline-flex px-2 py-0.5 rounded-full border {{ $badge }}">
                                            {{ $st }}
                                        </span>
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ $r['code_incident'] ?? '-' }} • {{ $r['type_reponse'] ?? '-' }}
                                    </div>
                                </div>

                                <div class="text-xs text-gray-500">
                                    {{ \Illuminate\Support\Carbon::parse($r['created_at'])->format('Y-m-d') }}
                                </div>
                            </div>

                            <div class="text-sm text-gray-700 mt-2">
                                <span class="font-medium">{{ $r['provider_name'] ?? '-' }}</span>
                                @if (!empty($r['focalpoint_number']))
                                    <span class="text-gray-500"> • {{ $r['focalpoint_number'] }}</span>
                                @endif
                            </div>

                            <div class="mt-2 flex justify-end">
                                <a href="{{ route('incidents.show', $r['id_incident']) }}"
                                    class="text-sm text-sky-800 hover:underline">
                                    Voir incident →
                                </a>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </x-ui-card>
    </div>
</div>
