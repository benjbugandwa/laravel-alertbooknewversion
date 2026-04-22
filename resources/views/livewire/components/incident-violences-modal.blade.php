<div>
    @if ($open)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-data
            x-on:keydown.escape.window="$wire.set('open', false)">
            <div class="absolute inset-0 bg-black/50" wire:click="$set('open', false)"></div>

            <div class="relative w-full max-w-3xl bg-white rounded-2xl shadow-xl border max-h-[85vh] flex flex-col">
                <div class="px-5 py-4 border-b flex items-center justify-between shrink-0">
                    <div class="font-semibold">Types de violences</div>
                    <button type="button" class="opacity-60 hover:opacity-100" wire:click="$set('open', false)">✕</button>
                </div>

                <div class="p-5 space-y-4 overflow-y-auto">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                        <div class="md:col-span-2">
                            <x-ui-input label="Recherche" placeholder="Ex: Viol, violence physique…"
                                wire:model.live="q" />
                        </div>
                        <div class="text-sm text-gray-500">
                            Coche une ou plusieurs violences.
                        </div>
                    </div>

                    @forelse($this->violencesGrouped as $cat => $items)
                        <div class="border rounded-xl overflow-hidden">
                            <div class="px-4 py-2 bg-gray-50 text-sm font-semibold">
                                {{ $cat }}
                            </div>

                            <div class="p-4 space-y-3">
                                @foreach ($items as $v)
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 items-start">
                                        <label class="inline-flex items-center gap-2 text-sm">
                                            <input type="checkbox" class="rounded border-gray-300"
                                                wire:model.live="selected.{{ $v['id'] }}">
                                            <span class="font-medium">{{ $v['name'] }}</span>
                                        </label>

                                        <div class="md:col-span-2">
                                            @if (!empty($selected[$v['id']]))
                                                <input type="text"
                                                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm"
                                                    placeholder="Description (optionnel)"
                                                    wire:model.defer="descriptions.{{ $v['id'] }}">
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="text-sm text-gray-600">Aucune violence trouvée.</div>
                    @endforelse
                </div>

                <div class="px-5 py-4 border-t bg-white shrink-0 flex justify-end gap-2">
                    <x-ui-button variant="secondary" wire:click="$set('open', false)">Annuler</x-ui-button>
                    <x-ui-button wire:click="save" wire:loading.attr="disabled">
                        <span wire:loading.remove>Enregistrer</span>
                        <span wire:loading>Traitement…</span>
                    </x-ui-button>
                </div>
            </div>
        </div>
    @endif
</div>
