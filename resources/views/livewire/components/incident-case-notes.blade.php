<div class="space-y-4">

    <x-ui-card>
        <div class="flex items-center justify-between mb-3">
            <div class="text-sm font-semibold">Notes de dossier</div>
            <div class="text-xs text-gray-500">{{ $this->notes->count() }} note(s)</div>
        </div>

        {{-- Form add note --}}
        @if (in_array(auth()->user()->user_role, ['superadmin', 'admin', 'superviseur']))
            <div class="space-y-3">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
                    <div class="md:col-span-2">
                        <label class="text-sm font-medium text-gray-700">Note</label>
                        <textarea wire:model="form.case_note" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm"
                            rows="3" placeholder="Ajouter une note de suivi..."></textarea>

                        @error('form.case_note')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                            <input type="checkbox" class="rounded border-gray-300"
                                wire:model="form.is_confidential">
                            <span>Confidentiel</span>
                        </label>

                        <div>
                            <input type="file" wire:model="file" class="block w-full text-sm">
                            @error('file')
                                <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                            <div class="text-xs text-gray-500 mt-1">PDF/JPG/PNG — 4MB max.</div>
                        </div>

                        <x-ui-button wire:click="save" wire:loading.attr="disabled" class="w-full">
                            <span wire:loading.remove>Ajouter</span>
                            <span wire:loading>Traitement…</span>
                        </x-ui-button>
                    </div>
                </div>
            </div>
        @endif
    </x-ui-card>

    {{-- Notes list --}}
    <x-ui-card>
        @if ($this->notes->isEmpty())
            <div class="text-sm text-gray-500">Aucune note pour cet incident.</div>
        @else
            <div class="space-y-4">
                @foreach ($this->notes as $n)
                    <div class="border rounded-xl p-4">
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <div class="text-sm font-semibold text-gray-900">
                                        {{ $n->author->name ?? '—' }}
                                    </div>

                                    @if ($n->is_confidential)
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 text-xs rounded-full bg-red-100 text-red-700 border border-red-200">
                                            Confidentiel
                                        </span>
                                    @endif
                                </div>

                                <div class="text-xs text-gray-500 mt-1">
                                    {{ optional($n->created_at)->format('Y-m-d H:i') }}
                                </div>
                            </div>

                            @if (in_array(auth()->user()->user_role, ['superadmin', 'admin', 'superviseur']))
                                <x-ui-button size="sm" variant="secondary"
                                    wire:click="openEdit({{ $n->id }})">
                                    Éditer
                                </x-ui-button>
                            @endif
                        </div>

                        <div class="mt-3 text-sm text-gray-700 whitespace-pre-line">
                            {{ $n->case_note }}
                        </div>

                        @if ($n->file_path)
                            <div class="mt-3">
                                <a href="{{ asset('storage/' . $n->file_path) }}" target="_blank"
                                    class="text-sm text-gray-900 hover:underline">
                                    📎 Voir la pièce jointe
                                </a>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </x-ui-card>

    {{-- Edit modal --}}
    @if ($showEditModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-data
            x-on:keydown.escape.window="$wire.set('showEditModal', false)">
            <div class="absolute inset-0 bg-black/50" wire:click="$set('showEditModal', false)"></div>

            <div class="relative w-full max-w-xl bg-white rounded-2xl shadow-xl border max-h-[80vh] flex flex-col">
                <div class="px-5 py-4 border-b flex items-center justify-between">
                    <div class="font-semibold">Modifier la note</div>
                    <button class="opacity-60 hover:opacity-100" wire:click="$set('showEditModal', false)">✕</button>
                </div>

                <div class="p-5 space-y-3 overflow-y-auto">
                    <div>
                        <label class="text-sm font-medium text-gray-700">Note</label>
                        <textarea wire:model="editForm.case_note" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm"
                            rows="4"></textarea>
                        @error('editForm.case_note')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                        @enderror
                    </div>

                    <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" class="rounded border-gray-300"
                            wire:model="editForm.is_confidential">
                        <span>Confidentiel</span>
                    </label>

                    <div>
                        <input type="file" wire:model="editFile" class="block w-full text-sm">
                        @error('editFile')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                        @enderror
                        <div class="text-xs text-gray-500 mt-1">Remplacer la pièce jointe (optionnel).</div>
                    </div>
                </div>

                <div class="px-5 py-4 border-t bg-white flex justify-end gap-2">
                    <x-ui-button variant="secondary" wire:click="$set('showEditModal', false)">Annuler</x-ui-button>
                    <x-ui-button wire:click="update" wire:loading.attr="disabled">
                        <span wire:loading.remove>Enregistrer</span>
                        <span wire:loading>Traitement…</span>
                    </x-ui-button>
                </div>
            </div>
        </div>
    @endif

</div>
