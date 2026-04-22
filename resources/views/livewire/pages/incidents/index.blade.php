<div class="space-y-6 relative" x-data x-on:open-url.window="window.open($event.detail.url, '_blank')">
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
            <div class="text-2xl font-bold">Incidents</div>
            <div class="text-sm text-gray-600">
                Les incidents archivés ne sont pas visibles. Validation par superviseur.
            </div>
        </div>

        <x-ui-button wire:click="openCreate">
            + Nouvel incident
        </x-ui-button>
        <x-ui-button variant="secondary" x-on:click="$dispatch('openIncidentsExport')">
            Exporter
        </x-ui-button>
    </div>

    <x-ui-card>
        <div class="grid grid-cols-1 lg:grid-cols-6 gap-4">
            <x-ui-input label="Recherche" placeholder="INC-000001…" wire:model.live="q" />

            <div class="space-y-1">
                <label class="text-sm font-medium text-gray-700">Statut</label>
                <select wire:model.live="f_status"
                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white">
                    <option value="">Tous</option>
                    @foreach ($statuses as $st)
                        @if ($st !== 'Archivé')
                            <option value="{{ $st }}">{{ $st }}</option>
                        @endif
                    @endforeach
                </select>
            </div>

            <div class="space-y-1">
                <label class="text-sm font-medium text-gray-700">Sévérité</label>
                <select wire:model.live="f_severite"
                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white">
                    <option value="">Toutes</option>
                    <option value="Faible">Faible</option>
                    <option value="Moyenne">Moyenne</option>
                    <option value="Élevée">Élevée</option>
                    <option value="Critique">Critique</option>
                </select>
            </div>

            {{-- Province (superadmin seulement) --}}
            @if (auth()->user()->user_role === 'superadmin')
                <div class="space-y-1">
                    <label class="text-sm font-medium text-gray-700">Province</label>
                    <select wire:model.live="f_province"
                        class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white">
                        <option value="">Toutes</option>
                        @foreach ($this->provinces as $p)
                            <option value="{{ $p['code'] }}">{{ $p['name'] }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="space-y-1">
                <label class="text-sm font-medium text-gray-700">Territoire</label>
                <select wire:model.live="f_territoire"
                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white">
                    <option value="">Tous</option>
                    @foreach ($this->territoires as $t)
                        <option value="{{ $t['code'] }}">{{ $t['name'] }}</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1">
                <label class="text-sm font-medium text-gray-700">Zone de santé</label>
                <select wire:model.live="f_zone"
                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white">
                    <option value="">Toutes</option>
                    @foreach ($this->zones as $z)
                        <option value="{{ $z['code'] }}">{{ $z['name'] }}</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1">
                <label class="text-sm font-medium text-gray-700">Du</label>
                <input type="date" wire:model.live="date_from"
                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white" />
            </div>

            <div class="space-y-1">
                <label class="text-sm font-medium text-gray-700">Au</label>
                <input type="date" wire:model.live="date_to"
                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white" />
            </div>

            <div class="space-y-1">
                <label class="text-sm font-medium text-gray-700">Pagination</label>
                <select wire:model.live="perPage"
                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white">
                    <option value="10">10 / page</option>
                    <option value="25">25 / page</option>
                    <option value="50">50 / page</option>
                </select>
            </div>
        </div>
    </x-ui-card>

    <x-ui-table :headers="['Code', 'Date', 'Violences', 'Sévérité', 'Statut', 'Localisation', 'Actions']">
        @forelse($incidents as $inc)
            <tr>
                <td class="px-4 py-3 font-medium">{{ $inc->code_incident }}</td>
                <td class="px-4 py-3">{{ optional($inc->date_incident)->format('Y-m-d') }}</td>
                <td class="px-4 py-3 text-sm text-gray-700">
                    @php
                        $names = $inc->violences?->pluck('violence_name')->take(2) ?? collect();
                        $more = max(0, ($inc->violences?->count() ?? 0) - 2);
                    @endphp

                    @if ($names->isEmpty())
                        <span class="text-gray-400">—</span>
                    @else
                        {{ $names->join(', ') }} @if ($more > 0)
                            <span class="text-gray-500">(+{{ $more }})</span>
                        @endif
                    @endif
                </td>
                <td class="px-4 py-3">{{ $inc->severite ?? '-' }}</td>
                <td class="px-4 py-3">
                    @php
                        $status = $inc->statut_incident;
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
                </td>
                <td class="px-4 py-3">
                    {{ $inc->province_name ?? '-' }} |
                    {{ $inc->localite ?? '-' }}
                </td>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2">
                        {{-- WhatsApp --}}
                        <button type="button"
                            class="h-9 w-9 inline-flex items-center justify-center rounded-lg border border-gray-200 hover:bg-gray-50"
                            wire:click="shareWhatsapp('{{ $inc->id }}')" title="Partager WhatsApp">
                            {{-- icon whatsapp (svg simple) --}}
                            <svg viewBox="0 0 32 32" class="h-5 w-5" fill="currentColor" aria-hidden="true">
                                <path
                                    d="M19.11 17.53c-.28-.14-1.67-.82-1.93-.91-.26-.09-.45-.14-.64.14-.19.28-.73.91-.9 1.1-.17.19-.33.21-.61.07-.28-.14-1.18-.44-2.25-1.39-.83-.74-1.39-1.66-1.56-1.94-.16-.28-.02-.43.12-.57.13-.13.28-.33.42-.49.14-.16.19-.28.28-.47.09-.19.05-.35-.02-.49-.07-.14-.64-1.54-.88-2.11-.23-.55-.47-.48-.64-.49h-.55c-.19 0-.49.07-.75.35-.26.28-.99.97-.99 2.37 0 1.4 1.02 2.75 1.16 2.94.14.19 2.01 3.07 4.87 4.31.68.29 1.2.46 1.61.59.68.22 1.29.19 1.77.12.54-.08 1.67-.68 1.91-1.33.23-.65.23-1.2.16-1.33-.07-.14-.26-.21-.54-.35z" />
                                <path
                                    d="M16.02 3C9.37 3 4 8.37 4 15c0 2.32.67 4.49 1.83 6.33L4 29l7.87-1.79A11.9 11.9 0 0 0 16.02 27C22.65 27 28 21.63 28 15S22.65 3 16.02 3zm0 21.78c-1.95 0-3.77-.57-5.31-1.56l-.38-.24-4.66 1.06 1-4.53-.25-.37A9.72 9.72 0 1 1 16.02 24.78z" />
                            </svg>
                        </button>

                        {{-- Dropdown actions --}}
                        <div x-data="{ open: false }" class="relative">
                            <button type="button"
                                class="bg-onu text-white hover:bg-onu-dark rounded-lg px-3 py-1.5 text-sm transition"
                                @click="open=!open">
                                Actions
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>

                            <div x-cloak x-show="open" @click.outside="open=false"
                                class="absolute right-0 mt-2 w-48 rounded-xl border bg-white shadow-lg overflow-hidden z-50">

                                {{-- Show Details Incidents --}}
                                <a href="{{ route('incidents.show', $inc->id) }}"
                                    class="block w-full text-left px-4 py-2 text-sm hover:bg-gray-50">
                                    Détails
                                </a>


                                {{-- Éditer --}}
                                <button class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50 disabled:opacity-50"
                                    wire:click="openEdit('{{ $inc->id }}')" @disabled(auth()->user()->user_role === 'moniteur' || in_array($inc->statut_incident, ['Cloturée', 'Archivé']))>
                                    Éditer
                                </button>

                                {{-- Violation lies à l'incident --}}
                                <button class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50"
                                    wire:click="$dispatch('openIncidentViolences', '{{ $inc->id }}')">
                                    Types de violences
                                </button>

                                {{-- Assigner --}}
                                <button class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50 disabled:opacity-50"
                                    wire:click="openAssign('{{ $inc->id }}')" @disabled(auth()->user()->user_role === 'moniteur' || in_array($inc->statut_incident, ['Cloturée', 'Archivé']))>
                                    Assigner
                                </button>

                                {{-- Valider --}}
                                <button class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50 disabled:opacity-50"
                                    wire:click="askConfirmValidate('{{ $inc->id }}')"
                                    @disabled(auth()->user()->user_role === 'moniteur' ||
                                            in_array($inc->statut_incident, ['Cloturée', 'Archivé']) ||
                                            $inc->statut_incident === 'Validé')>
                                    Valider
                                </button>

                                <div class="h-px bg-gray-100"></div>

                                {{-- Archiver --}}
                                <button
                                    class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50 text-red-600 disabled:opacity-50"
                                    wire:click="askConfirmArchive('{{ $inc->id }}')"
                                    @disabled(auth()->user()->user_role === 'moniteur' || in_array($inc->statut_incident, ['Cloturée', 'Archivé']))>
                                    Archiver
                                </button>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td class="px-4 py-6 text-center text-gray-600" colspan="6">
                    Aucun incident trouvé.
                </td>
            </tr>
        @endforelse
    </x-ui-table>

    <div>
        {{ $incidents->links() }}
    </div>

    {{-- Modal Create/Edit (scrollable + footer sticky) --}}
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-data
            x-on:keydown.escape.window="$wire.set('showModal', false)">
            <div class="absolute inset-0 bg-black/50" wire:click="$set('showModal', false)"></div>

            <div class="relative w-full max-w-3xl bg-white rounded-2xl shadow-xl border max-h-[85vh] flex flex-col">
                <div class="px-5 py-4 border-b flex items-center justify-between shrink-0">
                    <div class="font-semibold">{{ $editing ? 'Éditer incident' : 'Nouveau incident' }}</div>
                    <button type="button" class="opacity-60 hover:opacity-100"
                        wire:click="$set('showModal', false)">✕</button>
                </div>

                <div class="p-5 space-y-4 overflow-y-auto">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-gray-700">Date incident *</label>
                            <input type="date" wire:model.defer="form.date_incident"
                                max="{{ now()->toDateString() }}"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white">
                            @error('form.date_incident')
                                <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>



                        <div class="space-y-1">
                            <label class="text-sm font-medium text-gray-700">Sévérité *</label>
                            <select wire:model.defer="form.severite"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white">
                                @foreach ($this->severityOptions as $opt)
                                    <option value="{{ $opt }}">{{ $opt }}</option>
                                @endforeach
                            </select>
                            @error('form.severite')
                                <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>



                        <div class="space-y-1">
                            <label class="text-sm font-medium text-gray-700">Statut *</label>
                            <select wire:model.defer="form.statut_incident"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white">
                                <option value="En attente">En attente</option>
                                <option value="Validé">Validé</option>
                                <option value="Cloturée">Cloturée</option>
                                <option value="Archivé">Archivé</option>
                            </select>
                            @error('form.statut_incident')
                                <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Province rattachée automatiquement pour non-superadmin --}}
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-gray-700">Province *</label>
                            <select wire:model.live="form.code_province"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white"
                                @disabled(auth()->user()->user_role !== 'superadmin')>
                                @if (auth()->user()->user_role !== 'superadmin')
                                    <option value="{{ auth()->user()->code_province }}">
                                        {{ auth()->user()->code_province }}</option>
                                @else
                                    <option value="">-- Sélectionner --</option>
                                    @foreach ($this->provinces as $p)
                                        <option value="{{ $p['code'] }}">{{ $p['name'] }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('form.code_province')
                                <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="text-sm font-medium text-gray-700">Territoire</label>
                            <select wire:model.live="form.code_territoire"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white">
                                <option value="">-- Sélectionner --</option>
                                @foreach ($this->territoires as $t)
                                    <option value="{{ $t['code'] }}">{{ $t['name'] }}</option>
                                @endforeach
                            </select>
                            @error('form.code_territoire')
                                <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="text-sm font-medium text-gray-700">Zone de santé</label>
                            <select wire:model.defer="form.code_zonesante"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white">
                                <option value="">-- Sélectionner --</option>
                                @foreach ($this->zones as $z)
                                    <option value="{{ $z['code'] }}">{{ $z['name'] }}</option>
                                @endforeach
                            </select>
                            @error('form.code_zonesante')
                                <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="text-sm font-medium text-gray-700">Survivant (optionnel)</label>
                            <select wire:model.defer="form.survivant_id"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white">
                                <option value="">-- Aucun --</option>
                                @foreach ($this->survivants as $s)
                                    <option value="{{ $s->id }}">{{ $s->code_survivant }} —
                                        {{ $s->full_name }}</option>
                                @endforeach
                            </select>
                            @error('form.survivant_id')
                                <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-ui-input label="Localité" wire:model.defer="form.localite" name="localite" />
                        <x-ui-input label="Source d'information" wire:model.defer="form.source_info"
                            name="source_info" />
                        <x-ui-input label="Auteur présumé (optionnel)" wire:model.defer="form.auteur_presume"
                            name="auteur_presume" />

                        <div class="space-y-1">
                            <label class="text-sm font-medium text-gray-700">Confidentialité *</label>
                            <select wire:model.defer="form.confidentiality_level"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white">
                                @foreach ($this->confidentialityOptions as $opt)
                                    <option value="{{ $opt }}">{{ $opt }}</option>
                                @endforeach
                            </select>
                            @error('form.confidentiality_level')
                                <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-700">Description des faits (sensible)</label>
                        <textarea wire:model.defer="form.description_faits" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm"
                            rows="4"></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-start">
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-gray-700">Photo (optionnel)</label>
                            <input type="file" wire:model="photo" class="block w-full text-sm"
                                accept=".jpg,.jpeg,.png" />
                            @error('photo')
                                <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                            <div class="text-xs text-gray-500">JPG/PNG — 2MB max.</div>
                        </div>

                        @if ($photo)
                            <div class="space-y-1">
                                <label class="text-sm font-medium text-gray-700">Prévisualisation</label>
                                <img src="{{ $photo->temporaryUrl() }}"
                                    class="w-full max-h-40 object-cover rounded-lg border" alt="preview">
                            </div>
                        @endif
                    </div>

                    @if ($errors->any())
                        <div class="text-sm text-red-600">Veuillez corriger les champs en erreur.</div>
                    @endif
                </div>

                <div class="px-5 py-4 border-t bg-white shrink-0 flex justify-end gap-2">
                    <x-ui-button variant="secondary" wire:click="$set('showModal', false)">Annuler</x-ui-button>
                    <x-ui-button wire:click="save" wire:loading.attr="disabled">
                        <span wire:loading.remove>{{ $editing ? 'Enregistrer' : 'Créer' }}</span>
                        <span wire:loading>Traitement…</span>
                    </x-ui-button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Assign Incident --}}
    @if ($showAssignModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-data
            x-on:keydown.escape.window="$wire.set('showAssignModal', false)">
            <div class="absolute inset-0 bg-black/50" wire:click="$set('showAssignModal', false)"></div>

            <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-xl border max-h-[80vh] flex flex-col">
                <div class="px-5 py-4 border-b flex items-center justify-between shrink-0">
                    <div class="font-semibold">Assigner à un superviseur</div>
                    <button type="button" class="opacity-60 hover:opacity-100"
                        wire:click="$set('showAssignModal', false)">✕</button>
                </div>

                <div class="p-5 space-y-4 overflow-y-auto">
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-700">Superviseur</label>
                        <select wire:model.defer="assignTo"
                            class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white">
                            <option value="">-- Sélectionner --</option>
                            @foreach ($this->superviseursOptions as $u)
                                <option value="{{ $u['id'] }}">{{ $u['name'] }} ({{ $u['email'] }})
                                </option>
                            @endforeach
                        </select>
                        @error('assignTo')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="text-xs text-gray-500">
                        L’assignation est limitée aux superviseurs actifs de la province concernée.
                    </div>
                </div>

                <div class="px-5 py-4 border-t bg-white shrink-0 flex justify-end gap-2">
                    <x-ui-button variant="secondary" wire:click="$set('showAssignModal', false)">Annuler</x-ui-button>
                    <x-ui-button wire:click="assign" wire:loading.attr="disabled">
                        <span wire:loading.remove>Assigner</span>
                        <span wire:loading>Traitement…</span>
                    </x-ui-button>
                </div>
            </div>
        </div>
    @endif


    {{-- Modale de confirmation --}}
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

                    <x-ui-button :variant="$confirmAction === 'confirmArchive' ? 'danger' : 'primary'" wire:click="runConfirmAction" wire:loading.attr="disabled">
                        <span wire:loading.remove>Confirmer</span>
                        <span wire:loading>Traitement…</span>
                    </x-ui-button>
                </div>
            </div>
        </div>
    @endif

    <livewire:components.incident-violences-modal />

    <livewire:components.incidents-export-modal />

</div>
