<div class="space-y-6 relative">
    <div wire:loading class="absolute inset-0 z-50 flex items-center justify-center bg-white/50 backdrop-blur-sm rounded-2xl">
        <div class="flex flex-col items-center gap-2">
            <svg class="animate-spin h-8 w-8 text-onu" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-sm font-medium text-gray-700">Chargement...</span>
        </div>
    </div>
    <div class="flex items-start justify-between gap-4">
        <div>
            <div class="text-2xl font-bold">Alerte {{ $incident->code_incident }}</div>
            <div class="text-sm text-gray-600">
                Détails de l’alerte
            </div>
        </div>


        <div class="flex items-center gap-2">
            <a href="{{ route('incidents.index') }}" class="text-sm text-gray-700 hover:underline">
                ← Retour
            </a>

            <a href="{{ route('incidents.print', $incident->id) }}"
                class="inline-flex items-center gap-2 h-10 px-4 rounded-lg bg-white border border-gray-200 hover:bg-gray-50">
                🖨️ Imprimer
            </a>

            @if (in_array(auth()->user()->user_role, ['superadmin', 'admin', 'superviseur']) &&
                    !in_array($incident->statut_incident, ['Cloturée', 'Archivé']))
                {{-- Valider (si pas déjà validé) --}}
                @if ($incident->statut_incident !== 'Validé')
                    <x-ui-button wire:click="askConfirmValidate">
                        Valider
                    </x-ui-button>
                @endif

                {{-- Archiver (rouge) --}}
                <x-ui-button variant="danger" wire:click="askConfirmArchive">
                    Archiver
                </x-ui-button>
            @endif
        </div>
    </div>

    {{-- Cartes Détails de l'incident --}}

    <x-ui-card>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
                <span class="text-gray-500">Statut :</span>

                @php
                    $status = $incident->statut_incident;
                    $classes = match ($status) {
                        'En attente' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                        'Validé' => 'bg-green-100 text-green-800 border-green-200',
                        'Cloturée' => 'bg-gray-100 text-gray-700 border-gray-200',
                        'Archivé' => 'bg-gray-200 text-gray-600 border-gray-300',
                        default => 'bg-gray-100 text-gray-700 border-gray-200',
                    };
                @endphp

                <span
                    class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full border {{ $classes }}">
                    {{ $status }}
                </span>


            </div>
            <div><span class="text-gray-500">Sévérité :</span> <span
                    class="font-medium">{{ $incident->severite }}</span>
            </div>

            <div><span class="text-gray-500">Date de l'alerte :</span> <span
                    class="font-medium">{{ optional($incident->date_incident)->format('Y-m-d') }}</span></div>
            <div><span class="text-gray-500">Événement :</span> <span
                    class="font-medium">{{ $incident->evenement->nom_evenement ?? '-' }}</span></div>

            <div><span class="text-gray-500">Province :</span> <span class="font-medium">{{ $incident->province->nom_province ?? '-' }}</span></div>
            <div><span class="text-gray-500">Territoire :</span> <span class="font-medium">{{ $incident->territoire->nom_territoire ?? '-' }}</span></div>
            <div><span class="text-gray-500">Chefferie :</span> <span class="font-medium">{{ $incident->chefferie->nom_chefferie ?? '-' }}</span></div>
            <div><span class="text-gray-500">Groupement :</span> <span class="font-medium">{{ $incident->groupement->nom_groupement ?? '-' }}</span></div>
            <div><span class="text-gray-500">Zone de santé :</span> <span class="font-medium">{{ $incident->zoneSante->nom_zonesante ?? '-' }}</span></div>
            <div><span class="text-gray-500">Aire de santé :</span> <span class="font-medium">{{ $incident->aireSante->nom_airesante ?? '-' }}</span></div>

            <div class="md:col-span-2"><span class="text-gray-500">Localité :</span> <span
                    class="font-medium">{{ $incident->localite ?? '-' }}</span></div>

            <div class="md:col-span-2">
                <div class="text-gray-500 mb-1">Description</div>
                <div class="whitespace-pre-line">{{ $incident->description_faits ?? '-' }}</div>
            </div>
        </div>
    </x-ui-card>

    {{-- Cartes violences liés à l'incident --}}
    <x-ui-card>
        <div class="flex items-center justify-between mb-3">
            <div class="text-sm font-semibold">Violences signalées</div>
            <div class="text-xs text-gray-500">
                {{ $incident->violences->count() }} type(s)
            </div>
        </div>

        @php
            $grouped = $incident->violences->groupBy(fn($v) => $v->categorie_name ?: 'Autres');
        @endphp

        @if ($incident->violences->isEmpty())
            <div class="text-sm text-gray-500">Aucune violence sélectionnée pour cet incident.</div>
        @else
            <div class="space-y-4">
                @foreach ($grouped as $cat => $items)
                    <div class="border rounded-xl overflow-hidden">
                        <div class="px-4 py-2 bg-gray-50 text-sm font-semibold">
                            {{ $cat }}
                        </div>

                        <div class="p-4 space-y-3">
                            @foreach ($items as $v)
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $v->violence_name }}
                                        </div>

                                        @if (!empty($v->pivot?->description_violence))
                                            <div class="text-sm text-gray-600 mt-1 whitespace-pre-line">
                                                {{ $v->pivot->description_violence }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="text-xs text-gray-500 whitespace-nowrap">
                                        @if (!empty($v->pivot?->created_at))
                                            {{ \Carbon\Carbon::parse($v->pivot->created_at)->format('Y-m-d H:i') }}
                                        @endif
                                    </div>
                                </div>

                                @if (!$loop->last)
                                    <div class="h-px bg-gray-100"></div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </x-ui-card>

    @if (!in_array($incident->statut_incident, ['Cloturée', 'Archivé']) && auth()->user()->user_role !== 'moniteur')
        <x-ui-button variant="secondary" wire:click="$dispatch('openIncidentViolences', '{{ $incident->id }}')">
            Modifier les violences
        </x-ui-button>

        <livewire:components.incident-violences-modal />
    @endif

    {{-- Modale de confirmation avant validation --}}
    @if ($showConfirmModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-data
            x-on:keydown.escape.window="$wire.set('showConfirmModal', false)">
            <div class="absolute inset-0 bg-black/50" wire:click="$set('showConfirmModal', false)"></div>

            <div class="relative w-full max-w-md bg-white rounded-2xl shadow-xl border overflow-hidden">
                <div class="px-5 py-4 border-b flex items-center justify-between">
                    <div class="font-semibold">{{ $confirmTitle }}</div>
                    <button type="button" class="opacity-60 hover:opacity-100"
                        wire:click="$set('showConfirmModal', false)">✕</button>
                </div>

                <div class="p-5 text-sm text-gray-700">
                    {{ $confirmMessage }}
                </div>

                <div class="px-5 py-4 border-t bg-white flex justify-end gap-2">
                    <x-ui-button variant="secondary" wire:click="$set('showConfirmModal', false)">
                        Annuler
                    </x-ui-button>

                    <x-ui-button wire:click="runConfirmAction" wire:loading.attr="disabled">
                        <span wire:loading.remove>Confirmer</span>
                        <span wire:loading>Traitement…</span>
                    </x-ui-button>
                </div>
            </div>
        </div>
    @endif
    <livewire:components.incident-case-notes :incident-id="$incident->id" />
    <livewire:components.incident-referencements :incident-id="$incident->id" />

</div>
