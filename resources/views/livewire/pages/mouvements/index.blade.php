<div class="space-y-6 relative" x-data>
    {{-- Loader --}}
    <div wire:loading
        class="absolute inset-0 z-50 flex items-center justify-center bg-white/50 backdrop-blur-sm rounded-2xl">
        <div class="flex flex-col items-center gap-2">
            <svg class="animate-spin h-8 w-8 text-onu" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                </circle>
                <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                </path>
            </svg>
            <span class="text-sm font-medium text-gray-700">Chargement...</span>
        </div>
    </div>

    {{-- Incident Summary Header --}}
    <div class="bg-white border rounded-xl shadow-sm overflow-hidden">
        <div class="bg-gray-50 border-b px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('incidents.index') }}" class="text-gray-500 hover:text-gray-900 bg-white border border-gray-200 rounded-lg p-2 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <h1 class="text-xl font-bold text-gray-900">Incident: {{ $incident->code_incident }}</h1>
            </div>
            <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full border bg-blue-50 text-blue-800 border-blue-200">
                {{ $incident->statut_incident }}
            </span>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <div class="text-xs text-gray-500 uppercase tracking-wide">Date Incident</div>
                <div class="font-medium mt-1">{{ optional($incident->date_incident)->format('d/m/Y') ?? '—' }}</div>
            </div>
            <div>
                <div class="text-xs text-gray-500 uppercase tracking-wide">Type d'événement</div>
                <div class="font-medium mt-1">{{ $incident->evenement->nom_evenement ?? '—' }}</div>
            </div>
            <div>
                <div class="text-xs text-gray-500 uppercase tracking-wide">Sévérité</div>
                <div class="font-medium mt-1">{{ $incident->severite ?? '—' }}</div>
            </div>
            <div>
                <div class="text-xs text-gray-500 uppercase tracking-wide">Localisation (Incident)</div>
                <div class="font-medium mt-1">{{ $incident->province->nom_province ?? '—' }} / {{ $incident->localite ?? '—' }}</div>
            </div>
        </div>
    </div>

    {{-- Mouvements Section --}}
    <div class="flex items-start justify-between gap-4">
        <div>
            <div class="text-xl font-bold">Mouvements de population</div>
            <div class="text-sm text-gray-600">
                Gérez les mouvements liés à cet incident.
            </div>
        </div>

        @if ($this->canAddMouvement())
            <x-ui-button wire:click="openCreate">
                + Ajouter Mouvement
            </x-ui-button>
        @endif
    </div>

    <x-ui-card>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="space-y-1">
                <label class="text-sm font-medium text-gray-700">Type de mouvement</label>
                <select wire:model.live="f_type"
                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white">
                    <option value="">Tous</option>
                    <option value="Fuite">Fuite</option>
                    <option value="Retour">Retour</option>
                </select>
            </div>

            <div class="space-y-1">
                <label class="text-sm font-medium text-gray-700">Zone de provenance</label>
                <select wire:model.live="f_zone_prov"
                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white">
                    <option value="">Toutes</option>
                    @foreach ($all_zones as $z)
                        <option value="{{ $z->code_zonesante }}">{{ $z->nom_zonesante }}</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1">
                <label class="text-sm font-medium text-gray-700">Zone d'accueil</label>
                <select wire:model.live="f_zone_accl"
                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white">
                    <option value="">Toutes</option>
                    @foreach ($all_zones as $z)
                        <option value="{{ $z->code_zonesante }}">{{ $z->nom_zonesante }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </x-ui-card>

    <x-ui-table :headers="['Date', 'Type', 'Provenance', 'Accueil', 'Population (Ménages / Pers.)', 'Ajouté par', 'Actions']">
        @forelse($mouvements as $mv)
            <tr>
                <td class="px-4 py-3 text-sm text-gray-700">
                    {{ $mv->date_mouvement->format('d/m/Y') }}
                </td>
                <td class="px-4 py-3">
                    <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full border {{ $mv->type_mouvement === 'Fuite' ? 'bg-red-50 text-red-700 border-red-200' : 'bg-green-50 text-green-700 border-green-200' }}">
                        {{ $mv->type_mouvement }}
                    </span>
                </td>
                <td class="px-4 py-3 text-sm text-gray-700">
                    {{ $mv->territoireProv->nom_territoire ?? '-' }} / {{ $mv->localite_prov }}
                </td>
                <td class="px-4 py-3 text-sm text-gray-700">
                    {{ $mv->territoireAccl->nom_territoire ?? '-' }} / {{ $mv->localite_accl }}
                </td>
                <td class="px-4 py-3 text-sm text-gray-700">
                    {{ $mv->estim_nbre_menages }} / {{ $mv->estim_nbre_personnes }}
                </td>
                <td class="px-4 py-3 text-sm text-gray-700">
                    {{ $mv->creator->name ?? '—' }}
                </td>
                <td class="px-4 py-3">
                    @if ($this->canEditMouvement())
                        <button type="button"
                            class="h-9 w-9 inline-flex items-center justify-center rounded-lg border border-gray-200 hover:bg-gray-50 text-blue-600"
                            wire:click="openEdit({{ $mv->id }})" title="Modifier">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </button>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td class="px-4 py-6 text-center text-gray-600" colspan="7">
                    Aucun mouvement enregistré pour cet incident.
                </td>
            </tr>
        @endforelse
    </x-ui-table>

    <div>
        {{ $mouvements->links() }}
    </div>

    {{-- Modal Create/Edit --}}
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
            x-on:keydown.escape.window="$wire.set('showModal', false)">
            <div class="absolute inset-0 bg-black/50" wire:click="$set('showModal', false)"></div>

            <div class="relative w-full max-w-3xl bg-white rounded-2xl shadow-xl border max-h-[85vh] flex flex-col">
                <div class="px-5 py-4 border-b flex items-center justify-between shrink-0">
                    <div class="font-semibold">{{ $editing ? 'Modifier le mouvement' : 'Saisir un mouvement' }}</div>
                    <button type="button" class="opacity-60 hover:opacity-100"
                        wire:click="$set('showModal', false)">✕</button>
                </div>

                <div class="p-5 space-y-6 overflow-y-auto flex-1">
                    {{-- Informations de base --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-gray-700">Date du mouvement *</label>
                            <input type="date" wire:model.defer="form.date_mouvement" max="{{ now()->toDateString() }}"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white">
                            @error('form.date_mouvement') <div class="text-sm text-red-600">{{ $message }}</div> @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="text-sm font-medium text-gray-700">Type de mouvement *</label>
                            <select wire:model.defer="form.type_mouvement"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white">
                                <option value="">-- Sélectionner --</option>
                                <option value="Fuite">Fuite</option>
                                <option value="Retour">Retour</option>
                            </select>
                            @error('form.type_mouvement') <div class="text-sm text-red-600">{{ $message }}</div> @enderror
                        </div>

                        <x-ui-input label="Source d'information *" wire:model.defer="form.source_info" />
                    </div>

                    {{-- Localisations --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Provenance --}}
                        <div class="bg-orange-50 border border-orange-100 p-4 rounded-xl space-y-4">
                            <h3 class="text-sm font-bold text-orange-800 border-b border-orange-200 pb-2 uppercase tracking-wide">Zone de Provenance</h3>
                            
                            <div class="space-y-1">
                                <label class="text-sm font-medium text-gray-700">Province *</label>
                                <select wire:model.live="form.code_province_prov"
                                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white"
                                    @disabled(auth()->user()->user_role !== 'superadmin')>
                                    <option value="">-- Sélectionner --</option>
                                    @foreach ($provinces as $p)
                                        <option value="{{ $p['code_province'] }}">{{ $p['nom_province'] }}</option>
                                    @endforeach
                                </select>
                                @if(auth()->user()->user_role !== 'superadmin')
                                    <div class="text-xs text-gray-500">Lié à la province de l'incident.</div>
                                @endif
                                @error('form.code_province_prov') <div class="text-sm text-red-600">{{ $message }}</div> @enderror
                            </div>

                            <div class="space-y-1">
                                <label class="text-sm font-medium text-gray-700">Territoire *</label>
                                <select wire:model.live="form.code_territoire_prov"
                                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white"
                                    @disabled(auth()->user()->user_role !== 'superadmin')>
                                    <option value="">-- Sélectionner --</option>
                                    @foreach ($territoires_prov as $t)
                                        <option value="{{ $t['code_territoire'] }}">{{ $t['nom_territoire'] }}</option>
                                    @endforeach
                                </select>
                                @if(auth()->user()->user_role !== 'superadmin')
                                    <div class="text-xs text-gray-500">Lié au territoire de l'incident.</div>
                                @endif
                                @error('form.code_territoire_prov') <div class="text-sm text-red-600">{{ $message }}</div> @enderror
                            </div>

                            <div class="space-y-1">
                                <label class="text-sm font-medium text-gray-700">Zone de santé</label>
                                <select wire:model.defer="form.code_zonesante_prov"
                                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white">
                                    <option value="">-- Sélectionner --</option>
                                    @foreach ($zones_prov as $z)
                                        <option value="{{ $z['code_zonesante'] }}">{{ $z['nom_zonesante'] }}</option>
                                    @endforeach
                                </select>
                                @error('form.code_zonesante_prov') <div class="text-sm text-red-600">{{ $message }}</div> @enderror
                            </div>

                            <x-ui-input label="Localité * (ex: Village, Quartier)" wire:model.defer="form.localite_prov" />
                        </div>

                        {{-- Accueil --}}
                        <div class="bg-green-50 border border-green-100 p-4 rounded-xl space-y-4">
                            <h3 class="text-sm font-bold text-green-800 border-b border-green-200 pb-2 uppercase tracking-wide">Zone d'Accueil</h3>
                            
                            <div class="space-y-1">
                                <label class="text-sm font-medium text-gray-700">Province *</label>
                                <select wire:model.live="form.code_province_accl"
                                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white">
                                    <option value="">-- Sélectionner --</option>
                                    @foreach ($provinces as $p)
                                        <option value="{{ $p['code_province'] }}">{{ $p['nom_province'] }}</option>
                                    @endforeach
                                </select>
                                @error('form.code_province_accl') <div class="text-sm text-red-600">{{ $message }}</div> @enderror
                            </div>

                            <div class="space-y-1">
                                <label class="text-sm font-medium text-gray-700">Territoire *</label>
                                <select wire:model.live="form.code_territoire_accl"
                                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white">
                                    <option value="">-- Sélectionner --</option>
                                    @foreach ($territoires_accl as $t)
                                        <option value="{{ $t['code_territoire'] }}">{{ $t['nom_territoire'] }}</option>
                                    @endforeach
                                </select>
                                @error('form.code_territoire_accl') <div class="text-sm text-red-600">{{ $message }}</div> @enderror
                            </div>

                            <div class="space-y-1">
                                <label class="text-sm font-medium text-gray-700">Zone de santé</label>
                                <select wire:model.defer="form.code_zonesante_accl"
                                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white">
                                    <option value="">-- Sélectionner --</option>
                                    @foreach ($zones_accl as $z)
                                        <option value="{{ $z['code_zonesante'] }}">{{ $z['nom_zonesante'] }}</option>
                                    @endforeach
                                </select>
                                @error('form.code_zonesante_accl') <div class="text-sm text-red-600">{{ $message }}</div> @enderror
                            </div>

                            <x-ui-input label="Localité * (ex: Village, Quartier)" wire:model.defer="form.localite_accl" />
                        </div>
                    </div>

                    {{-- Population --}}
                    <div class="bg-gray-50 border border-gray-100 p-4 rounded-xl space-y-4">
                        <h3 class="text-sm font-bold text-gray-800 border-b pb-2 uppercase tracking-wide">Population et Logement</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="space-y-1">
                                <label class="text-sm font-medium text-gray-700">Nombre de Ménages estimé *</label>
                                <input type="number" min="1" wire:model.defer="form.estim_nbre_menages"
                                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white">
                                @error('form.estim_nbre_menages') <div class="text-sm text-red-600">{{ $message }}</div> @enderror
                            </div>

                            <div class="space-y-1">
                                <label class="text-sm font-medium text-gray-700">Nombre de Personnes estimé *</label>
                                <input type="number" min="1" wire:model.defer="form.estim_nbre_personnes"
                                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white">
                                @error('form.estim_nbre_personnes') <div class="text-sm text-red-600">{{ $message }}</div> @enderror
                            </div>

                            <div class="space-y-1">
                                <label class="text-sm font-medium text-gray-700">Type de logement</label>
                                <select wire:model.defer="form.type_logement"
                                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white">
                                    <option value="">-- Sélectionner --</option>
                                    <option value="Site spontané">Site spontané</option>
                                    <option value="Centre collectif">Centre collectif</option>
                                    <option value="Famille accueil">Famille accueil</option>
                                    <option value="Autre">Autre</option>
                                </select>
                                @error('form.type_logement') <div class="text-sm text-red-600">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-700">Remarques (optionnel)</label>
                        <textarea wire:model.defer="form.remarques_mouvement" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm"
                            rows="2"></textarea>
                        @error('form.remarques_mouvement') <div class="text-sm text-red-600">{{ $message }}</div> @enderror
                    </div>

                    @if ($errors->any())
                        <div class="text-sm text-red-600 p-3 bg-red-50 rounded-lg">Veuillez corriger les champs en erreur.</div>
                    @endif
                </div>

                <div class="px-5 py-4 border-t bg-white shrink-0 flex justify-end gap-2">
                    <x-ui-button variant="secondary" wire:click="$set('showModal', false)">Annuler</x-ui-button>
                    <x-ui-button wire:click="save" wire:loading.attr="disabled">
                        <span wire:loading.remove>{{ $editing ? 'Mettre à jour' : 'Ajouter' }}</span>
                        <span wire:loading>Traitement...</span>
                    </x-ui-button>
                </div>
            </div>
        </div>
    @endif
</div>
