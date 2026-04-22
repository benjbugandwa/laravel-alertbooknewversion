<div class="space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <div class="text-2xl font-bold">Organisations</div>
            <div class="text-sm text-gray-600">Gestion des organisations partenaires (admin/superadmin).</div>
        </div>

        <x-ui-button wire:click="openCreate">
            + Nouvelle organisation
        </x-ui-button>
    </div>

    <x-ui-card>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <x-ui-input label="Recherche" placeholder="Sigle, nom, catégorie…" wire:model.live="q" />

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

    <x-ui-table :headers="['Sigle', 'Nom', 'Catégorie', 'Secteurs', 'Actions']">
        @forelse($organisations as $o)
            <tr>
                <td class="px-4 py-3 font-medium">{{ $o->org_sigle ?? '—' }}</td>
                <td class="px-4 py-3">{{ $o->org_name }}</td>
                <td class="px-4 py-3">{{ $o->org_categorie ?? '—' }}</td>
                <td class="px-4 py-3 text-sm text-gray-700">
                    @php
                        $secs = collect($o->org_secteur_activite ?? []);
                        $first = $secs->take(2);
                        $more = max(0, $secs->count() - 2);
                    @endphp

                    @if ($secs->isEmpty())
                        <span class="text-gray-400">—</span>
                    @else
                        {{ $first->join(', ') }}
                        @if ($more > 0)
                            <span class="text-gray-500">(+{{ $more }})</span>
                        @endif
                    @endif
                </td>
                <td class="px-4 py-3">
                    <x-ui-button size="sm" variant="secondary" wire:click="openEdit({{ $o->id }})">
                        Éditer
                    </x-ui-button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="px-4 py-6 text-center text-gray-600">Aucune organisation trouvée.</td>
            </tr>
        @endforelse
    </x-ui-table>

    <div>
        {{ $organisations->links() }}
    </div>

    {{-- Modal Create/Edit --}}
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-data
            x-on:keydown.escape.window="$wire.set('showModal', false)">
            <div class="absolute inset-0 bg-black/50" wire:click="$set('showModal', false)"></div>

            <div class="relative w-full max-w-2xl bg-white rounded-2xl shadow-xl border max-h-[85vh] flex flex-col">
                <div class="px-5 py-4 border-b flex items-center justify-between shrink-0">
                    <div class="font-semibold">{{ $editing ? 'Modifier organisation' : 'Nouvelle organisation' }}</div>
                    <button type="button" class="opacity-60 hover:opacity-100"
                        wire:click="$set('showModal', false)">✕</button>
                </div>

                <div class="p-5 space-y-4 overflow-y-auto">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-ui-input label="Sigle (optionnel)" wire:model.defer="form.org_sigle"
                            placeholder="ex: UNHCR" />
                        <x-ui-input label="Nom *" wire:model.defer="form.org_name"
                            placeholder="ex: Haut Commissariat..." />
                    </div>

                    @error('form.org_name')
                        <div class="text-sm text-red-600">{{ $message }}</div>
                    @enderror
                    @error('form.org_sigle')
                        <div class="text-sm text-red-600">{{ $message }}</div>
                    @enderror

                    <div class="space-y-2">
                        <div class="text-sm font-medium text-gray-700">Secteurs d’activité (multi)</div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            @foreach ($secteurOptions as $opt)
                                <label class="inline-flex items-center gap-2 text-sm">
                                    <input type="checkbox" class="rounded border-gray-300"
                                        wire:model.defer="form.org_secteur_activite" value="{{ $opt }}">
                                    <span>{{ $opt }}</span>
                                </label>
                            @endforeach
                        </div>

                        @error('form.org_secteur_activite.*')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-700">Catégorie</label>
                        <select wire:model.defer="form.org_categorie"
                            class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white">
                            <option value="">-- Sélectionner --</option>
                            @foreach ($categorieOptions as $c)
                                <option value="{{ $c }}">{{ $c }}</option>
                            @endforeach
                        </select>
                        @error('form.org_categorie')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                        @enderror
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
</div>
