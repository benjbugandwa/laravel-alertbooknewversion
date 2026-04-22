<div class="space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <div class="text-2xl font-bold">Structures de prise en charge</div>
            <div class="text-sm text-gray-600">
                Gestion des partenaires (multi-provinces) et services proposés.
            </div>
        </div>

        <x-ui-button wire:click="openCreate">
            + Nouvelle structure
        </x-ui-button>
    </div>

    <x-ui-card>
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
            <x-ui-input label="Recherche" placeholder="Nom, email, focal point…" wire:model.live="q" />

            <div class="space-y-1">
                <label class="text-sm font-medium text-gray-700">Province</label>
                <select wire:model.live="f_province"
                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white">
                    <option value="">Toutes</option>
                    @foreach ($provinces as $p)
                        <option value="{{ $p['code'] }}">{{ $p['name'] }}</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1">
                <label class="text-sm font-medium text-gray-700">Type de service</label>
                <select wire:model.live="f_service"
                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white">
                    <option value="">Tous</option>
                    @foreach ($serviceOptions as $opt)
                        <option value="{{ $opt }}">{{ $opt }}</option>
                    @endforeach
                </select>
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

    <x-ui-table :headers="['Structure', 'Présence', 'Services', 'Point focal', 'Actions']">
        @forelse($providers as $p)
            <tr>
                <td class="px-4 py-3">
                    <div class="font-medium">{{ $p->provider_name }}</div>
                    <div class="text-xs text-gray-500">{{ $p->provider_location ? '' : '' }}</div>
                </td>

                <td class="px-4 py-3">
                    @php $locs = collect($p->provider_location ?? []); @endphp
                    @if ($locs->isEmpty())
                        <span class="text-gray-400">—</span>
                    @else
                        <div class="flex flex-wrap gap-1">
                            @foreach ($locs->take(4) as $code)
                                <span
                                    class="inline-flex items-center px-2 py-0.5 text-xs rounded-full border bg-gray-50">
                                    {{ $provinceMap[$code] ?? $code }}
                                </span>
                            @endforeach
                            @if ($locs->count() > 4)
                                <span class="text-xs text-gray-500">(+{{ $locs->count() - 4 }})</span>
                            @endif
                        </div>
                    @endif
                </td>

                <td class="px-4 py-3">
                    @php $svcs = collect($p->type_services_proposes ?? []); @endphp
                    @if ($svcs->isEmpty())
                        <span class="text-gray-400">—</span>
                    @else
                        <div class="flex flex-wrap gap-1">
                            @foreach ($svcs->take(3) as $s)
                                <span
                                    class="inline-flex items-center px-2 py-0.5 text-xs rounded-full border bg-blue-50 border-blue-200 text-blue-800">
                                    {{ $s }}
                                </span>
                            @endforeach
                            @if ($svcs->count() > 3)
                                <span class="text-xs text-gray-500">(+{{ $svcs->count() - 3 }})</span>
                            @endif
                        </div>
                    @endif
                </td>

                <td class="px-4 py-3 text-sm">
                    <div class="text-gray-900">{{ $p->focalpoint_name ?? '—' }}</div>
                    <div class="text-xs text-gray-500">
                        {{ $p->focalpoint_email ?? '' }}
                        {{ $p->focalpoint_number ? '• ' . $p->focalpoint_number : '' }}
                    </div>
                </td>

                <td class="px-4 py-3">
                    <x-ui-button size="sm" variant="secondary" wire:click="openEdit({{ $p->id }})">
                        Éditer
                    </x-ui-button>
                </td>
            </tr>
        @empty
            <tr>
                <td class="px-4 py-6 text-center text-gray-600" colspan="5">
                    Aucun partenaire trouvé.
                </td>
            </tr>
        @endforelse
    </x-ui-table>

    <div>{{ $providers->links() }}</div>

    {{-- Modal Create/Edit --}}
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-data
            x-on:keydown.escape.window="$wire.set('showModal', false)">
            <div class="absolute inset-0 bg-black/50" wire:click="$set('showModal', false)"></div>

            <div class="relative w-full max-w-3xl bg-white rounded-2xl shadow-xl border max-h-[85vh] flex flex-col">
                <div class="px-5 py-4 border-b flex items-center justify-between shrink-0">
                    <div class="font-semibold">{{ $editing ? 'Modifier la structure' : 'Nouvelle structure' }}</div>
                    <button type="button" class="opacity-60 hover:opacity-100"
                        wire:click="$set('showModal', false)">✕</button>
                </div>

                <div class="p-5 space-y-4 overflow-y-auto">
                    <x-ui-input label="Nom de la structure *" wire:model.defer="form.provider_name" />
                    @error('form.provider_name')
                        <div class="text-sm text-red-600">{{ $message }}</div>
                    @enderror

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-ui-input label="Nom point focal" wire:model.defer="form.focalpoint_name" />
                        <x-ui-input label="Email point focal" type="email" wire:model.defer="form.focalpoint_email" />
                        <x-ui-input label="Téléphone point focal" wire:model.defer="form.focalpoint_number" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Provinces (multi-select via checkboxes) --}}
                        <div class="space-y-2">
                            <div class="text-sm font-medium text-gray-700">Provinces couvertes *</div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @foreach ($provinces as $p)
                                    <label class="inline-flex items-center gap-2 text-sm">
                                        <input type="checkbox" class="rounded border-gray-300"
                                            wire:model.defer="form.provider_location" value="{{ $p['code'] }}">
                                        <span>{{ $p['name'] }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('form.provider_location')
                                <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Services (multi-select) --}}
                        <div class="space-y-2">
                            <div class="text-sm font-medium text-gray-700">Services proposés *</div>
                            <div class="grid grid-cols-1 gap-2">
                                @foreach ($serviceOptions as $opt)
                                    <label class="inline-flex items-center gap-2 text-sm">
                                        <input type="checkbox" class="rounded border-gray-300"
                                            wire:model.defer="form.type_services_proposes" value="{{ $opt }}">
                                        <span>{{ $opt }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('form.type_services_proposes')
                                <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
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
