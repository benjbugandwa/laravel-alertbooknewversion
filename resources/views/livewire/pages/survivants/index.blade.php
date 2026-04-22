<div class="space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <div class="text-2xl font-bold">Survivants</div>
            <div class="text-sm text-gray-600">
                Recherche par code ou nom complet, création/édition via modal.
            </div>
        </div>

        <x-ui-button wire:click="openCreate">
            + Nouveau survivant
        </x-ui-button>
    </div>

    <x-ui-card>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <x-ui-input label="Recherche" placeholder="SRV-00001, Nom…" wire:model.live="q" />

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

    <x-ui-table :headers="['Code', 'Nom complet', 'Âge', 'Sexe', 'Mineure', 'Téléphone tuteur', 'Actions']">
        @forelse($survivants as $s)
            <tr>
                <td class="px-4 py-3 font-medium">{{ $s->code_survivant }}</td>
                <td class="px-4 py-3">{{ $s->full_name }}</td>
                <td class="px-4 py-3">{{ $s->age_survivant ?? '-' }}</td>
                <td class="px-4 py-3">{{ $s->sexe_survivant ?? '-' }}</td>
                <td class="px-4 py-3">
                    @if ($s->est_mineure)
                        <span class="inline-flex px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-800">Oui</span>
                    @else
                        <span class="inline-flex px-2 py-1 text-xs rounded bg-gray-100 text-gray-700">Non</span>
                    @endif
                </td>
                <td class="px-4 py-3">{{ $s->tuteur_numero ?: '-' }}</td>
                <td class="px-4 py-3">
                    <x-ui-button size="sm" variant="secondary" wire:click="openEdit('{{ $s->id }}')">
                        Éditer
                    </x-ui-button>
                </td>
            </tr>
        @empty
            <tr>
                <td class="px-4 py-6 text-center text-gray-600" colspan="7">
                    Aucun survivant trouvé.
                </td>
            </tr>
        @endforelse
    </x-ui-table>

    <div>
        {{ $survivants->links() }}
    </div>

    {{-- Modal Create/Edit --}}
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-data
            x-on:keydown.escape.window="$wire.set('showModal', false)">
            {{-- Backdrop --}}
            <div class="absolute inset-0 bg-black/50" wire:click="$set('showModal', false)"></div>

            {{-- Modal --}}
            <div class="relative w-full max-w-2xl bg-white rounded-2xl shadow-xl border max-h-[85vh] flex flex-col">

                {{-- Header (fixe) --}}
                <div class="px-5 py-4 border-b flex items-center justify-between shrink-0">
                    <div class="font-semibold">
                        {{ $editing ? 'Éditer survivant' : 'Nouveau survivant' }}
                    </div>
                    <button type="button" class="opacity-60 hover:opacity-100" wire:click="$set('showModal', false)">
                        ✕
                    </button>
                </div>

                {{-- Body (scrollable) --}}
                <div class="p-5 space-y-4 overflow-y-auto">

                    {{-- Ligne 1 (md=4 colonnes) : Nom (2) + Âge (1) + Sexe (1) --}}
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="md:col-span-2">
                            <x-ui-input label="Nom complet *" wire:model.defer="form.full_name" name="full_name" />
                        </div>

                        <div>
                            <x-ui-input label="Âge" type="number" wire:model.live="form.age_survivant"
                                name="age_survivant" placeholder="ex: 16" />
                        </div>

                        <div class="space-y-1">
                            <label class="text-sm font-medium text-gray-700">Sexe</label>
                            <select wire:model.defer="form.sexe_survivant"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900">
                                <option value="">-- Sélectionner --</option>
                                <option value="Masculin">Masculin</option>
                                <option value="Féminin">Féminin</option>
                                <option value="Autre">Autre</option>
                            </select>
                            @error('form.sexe_survivant')
                                <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Ligne 2 : Statut matrimonial + Handicap + Mineure sur la même ligne --}}
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                        <div class="md:col-span-2 space-y-1">
                            <label class="text-sm font-medium text-gray-700">Statut matrimonial</label>
                            <select wire:model.defer="form.marital_status"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900">
                                <option value="">-- Sélectionner --</option>
                                <option value="Marié">Marié</option>
                                <option value="Célibataire">Célibataire</option>
                                <option value="Divorcé">Divorcé</option>
                                <option value="Autre">Autre</option>
                            </select>
                            @error('form.marital_status')
                                <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>

                        <label class="md:col-span-1 inline-flex items-center gap-2 text-sm text-gray-700 h-10">
                            <input type="checkbox" class="rounded border-gray-300"
                                wire:model.defer="form.disability_status">
                            <span>Handicap</span>
                        </label>


                    </div>

                    {{-- Ligne 3 : Tuteur Nom + Téléphone (même ligne) --}}
                    {{-- Tuteur : affiché seulement si mineure --}}
                    <div x-data="{ mineure: @entangle('form.est_mineure') }" x-cloak>
                        <template x-if="mineure">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <x-ui-input label="Nom du tuteur" wire:model.defer="form.tuteur_nom" name="tuteur_nom"
                                    placeholder="Obligatoire si mineure" />

                                <x-ui-input label="Téléphone du tuteur" wire:model.defer="form.tuteur_numero"
                                    name="tuteur_numero" placeholder="Obligatoire si mineure" />
                            </div>
                        </template>
                    </div>




                    {{-- Adresses --}}
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-700">Adresses</label>
                        <textarea wire:model.defer="form.adresses" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm"
                            rows="2" placeholder="Adresse, quartier, avenue..."></textarea>
                    </div>

                    {{-- Observations --}}
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-700">Observations</label>
                        <textarea wire:model.defer="form.observations" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm"
                            rows="3" placeholder="Notes utiles..."></textarea>
                    </div>

                    @if ($errors->any())
                        <div class="text-sm text-red-600">
                            Veuillez corriger les champs en erreur.
                        </div>
                    @endif
                </div>

                {{-- Footer (sticky) : boutons toujours visibles --}}
                <div class="px-5 py-4 border-t bg-white shrink-0">
                    <div class="flex justify-end gap-2">
                        <x-ui-button variant="secondary" wire:click="$set('showModal', false)">Annuler</x-ui-button>

                        <x-ui-button wire:click="save" wire:loading.attr="disabled">
                            <span wire:loading.remove>{{ $editing ? 'Enregistrer' : 'Créer' }}</span>
                            <span wire:loading>Traitement…</span>
                        </x-ui-button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
