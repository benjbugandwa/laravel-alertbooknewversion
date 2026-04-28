<div>
    {{-- Bouton optionnel (si tu veux l’utiliser directement) --}}
    {{-- <x-ui-button variant="secondary" wire:click="open">Exporter</x-ui-button> --}}

    @if ($show)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-data
            x-on:keydown.escape.window="$wire.close()">
            <div class="absolute inset-0 bg-black/50" wire:click="close"></div>

            <div class="relative w-full max-w-xl bg-white rounded-2xl shadow-xl border max-h-[85vh] flex flex-col">
                <div class="px-5 py-4 border-b flex items-center justify-between shrink-0">
                    <div class="font-semibold">Exporter la matrice des incidents</div>
                    <button type="button" class="opacity-60 hover:opacity-100" wire:click="close">✕</button>
                </div>

                <div class="p-5 space-y-4 overflow-y-auto">
                    <div class="text-sm text-gray-600">
                        Export CSV/Excel (incidents + notes + référencements) sur une période. Uniquement pour les incidents validés.
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-gray-700">Du *</label>
                            <input type="date" wire:model.defer="from"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white">
                            @error('from')
                                <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="text-sm font-medium text-gray-700">Au *</label>
                            <input type="date" wire:model.defer="to" max="{{ now()->toDateString() }}"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white">
                            @error('to')
                                <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-gray-700">Format *</label>
                            <select wire:model.defer="format"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white">
                                <option value="xlsx">Excel (.xlsx)</option>
                                <option value="csv">CSV (.csv)</option>
                            </select>
                            @error('format')
                                <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Province : uniquement superadmin --}}
                        @if (auth()->user()->user_role === 'superadmin')
                            <div class="space-y-1">
                                <label class="text-sm font-medium text-gray-700">Province</label>
                                <select wire:model.defer="province"
                                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white">
                                    <option value="">Toutes les provinces</option>
                                    @foreach ($this->provinces as $p)
                                        <option value="{{ $p['code'] }}">{{ $p['name'] }}</option>
                                    @endforeach
                                </select>
                                @error('province')
                                    <div class="text-sm text-red-600">{{ $message }}</div>
                                @enderror
                            </div>
                        @else
                            <div class="space-y-1">
                                <label class="text-sm font-medium text-gray-700">Province</label>
                                <div
                                    class="h-10 flex items-center px-3 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-700">
                                    {{ auth()->user()->code_province }}
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="rounded-xl border border-gray-200 p-4 space-y-3">
                        <div class="text-sm font-semibold">Données à inclure</div>

                        <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                            <input type="checkbox" class="rounded border-gray-300" wire:model.defer="include_notes">
                            <span>Inclure les notes</span>
                        </label>

                        <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                            <input type="checkbox" class="rounded border-gray-300" wire:model.defer="include_violences">
                            <span>Inclure les violences</span>
                        </label>

                        <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                            <input type="checkbox" class="rounded border-gray-300"
                                wire:model.defer="include_referencements">
                            <span>Inclure les référencements (2ᵉ feuille Excel)</span>
                        </label>

                        <div class="text-xs text-gray-500">
                            NB : l’option “référencements” nécessite le format <strong>XLSX</strong>.
                        </div>
                    </div>










                </div>

                <div class="px-5 py-4 border-t bg-white shrink-0 flex justify-end gap-2">
                    <x-ui-button variant="secondary" wire:click="close">
                        Annuler
                    </x-ui-button>

                    <x-ui-button wire:click="export" wire:loading.attr="disabled">
                        <span wire:loading.remove>Exporter</span>
                        <span wire:loading>Préparation…</span>
                    </x-ui-button>
                </div>
            </div>
        </div>
    @endif
    <x-ui-loading-overlay target="export" />
</div>
