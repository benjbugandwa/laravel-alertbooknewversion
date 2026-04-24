<div class="space-y-6 relative" x-data x-on:open-url.window="window.open($event.detail.url, '_blank')">
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

    <div class="flex items-start justify-between gap-4">
        <div>
            <div class="text-2xl font-bold">Documents</div>
            <div class="text-sm text-gray-600">
                Gérez les documents, rapports et autres ressources partagées.
            </div>
        </div>

        @if ($this->canEditOrAdd())
            <x-ui-button wire:click="openCreate">
                + Nouveau document
            </x-ui-button>
        @endif
    </div>

    <x-ui-card>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <x-ui-input label="Recherche" placeholder="Rechercher un document..." wire:model.live="q" />

            <div class="space-y-1">
                <label class="text-sm font-medium text-gray-700">Catégorie</label>
                <select wire:model.live="f_category"
                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white">
                    <option value="">Toutes les catégories</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat }}">{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </x-ui-card>

    <x-ui-table :headers="['Document', 'Catégorie', 'Ajouté par', 'Téléchargements', 'Date', 'Actions']">
        @forelse($documents as $doc)
            <tr>
                <td class="px-4 py-3">
                    <div class="font-medium text-gray-900">{{ $doc->doc_name }}</div>
                    <div class="text-xs text-gray-500">{{ $doc->original_name }} ({{ strtoupper($doc->file_type) }})</div>
                </td>
                <td class="px-4 py-3 text-sm text-gray-700">
                    <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full border bg-gray-100 text-gray-700 border-gray-200">
                        {{ $doc->doc_category ?? '-' }}
                    </span>
                </td>
                <td class="px-4 py-3 text-sm text-gray-700">
                    {{ $doc->uploader->name ?? 'Inconnu' }}
                </td>
                <td class="px-4 py-3 text-sm text-gray-700 text-center">
                    {{ $doc->download_count }}
                </td>
                <td class="px-4 py-3 text-sm text-gray-700">
                    {{ $doc->created_at->format('Y-m-d') }}
                </td>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2">
                        {{-- Download --}}
                        <button type="button"
                            class="h-9 w-9 inline-flex items-center justify-center rounded-lg border border-gray-200 hover:bg-gray-50 text-gray-600"
                            wire:click="download('{{ $doc->id }}')" title="Télécharger">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                        </button>

                        {{-- WhatsApp --}}
                        <button type="button"
                            class="h-9 w-9 inline-flex items-center justify-center rounded-lg border border-gray-200 hover:bg-gray-50 text-green-600"
                            wire:click="shareWhatsapp('{{ $doc->id }}')" title="Partager sur WhatsApp">
                            <svg viewBox="0 0 32 32" class="h-5 w-5" fill="currentColor">
                                <path d="M19.11 17.53c-.28-.14-1.67-.82-1.93-.91-.26-.09-.45-.14-.64.14-.19.28-.73.91-.9 1.1-.17.19-.33.21-.61.07-.28-.14-1.18-.44-2.25-1.39-.83-.74-1.39-1.66-1.56-1.94-.16-.28-.02-.43.12-.57.13-.13.28-.33.42-.49.14-.16.19-.28.28-.47.09-.19.05-.35-.02-.49-.07-.14-.64-1.54-.88-2.11-.23-.55-.47-.48-.64-.49h-.55c-.19 0-.49.07-.75.35-.26.28-.99.97-.99 2.37 0 1.4 1.02 2.75 1.16 2.94.14.19 2.01 3.07 4.87 4.31.68.29 1.2.46 1.61.59.68.22 1.29.19 1.77.12.54-.08 1.67-.68 1.91-1.33.23-.65.23-1.2.16-1.33-.07-.14-.26-.21-.54-.35z" />
                                <path d="M16.02 3C9.37 3 4 8.37 4 15c0 2.32.67 4.49 1.83 6.33L4 29l7.87-1.79A11.9 11.9 0 0 0 16.02 27C22.65 27 28 21.63 28 15S22.65 3 16.02 3zm0 21.78c-1.95 0-3.77-.57-5.31-1.56l-.38-.24-4.66 1.06 1-4.53-.25-.37A9.72 9.72 0 1 1 16.02 24.78z" />
                            </svg>
                        </button>

                        @if ($this->canEditOrAdd() && $doc->uploaded_by === auth()->id())
                            {{-- Edit --}}
                            <button type="button"
                                class="h-9 w-9 inline-flex items-center justify-center rounded-lg border border-gray-200 hover:bg-gray-50 text-blue-600"
                                wire:click="openEdit('{{ $doc->id }}')" title="Modifier">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                        @endif
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td class="px-4 py-6 text-center text-gray-600" colspan="6">
                    Aucun document trouvé.
                </td>
            </tr>
        @endforelse
    </x-ui-table>

    <div>
        {{ $documents->links() }}
    </div>

    {{-- Modal Create/Edit --}}
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-data
            x-on:keydown.escape.window="$wire.set('showModal', false)">
            <div class="absolute inset-0 bg-black/50" wire:click="$set('showModal', false)"></div>

            <div class="relative w-full max-w-2xl bg-white rounded-2xl shadow-xl border max-h-[85vh] flex flex-col">
                <div class="px-5 py-4 border-b flex items-center justify-between shrink-0">
                    <div class="font-semibold">{{ $editing ? 'Modifier le document' : 'Ajouter un document' }}</div>
                    <button type="button" class="opacity-60 hover:opacity-100"
                        wire:click="$set('showModal', false)">✕</button>
                </div>

                <div class="p-5 space-y-4 overflow-y-auto">
                    
                    <x-ui-input label="Nom du document *" wire:model.defer="form.doc_name" />
                    
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-700">Catégorie *</label>
                        <select wire:model.defer="form.doc_category"
                            class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white">
                            <option value="">-- Sélectionner --</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                        @error('form.doc_category')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-700">Fichier {{ $editing ? '(laisser vide pour conserver l\'actuel)' : '*' }}</label>
                        <input type="file" wire:model="file" class="block w-full text-sm rounded-lg border border-gray-200 p-2" />
                        <div class="text-xs text-gray-500 mt-1">Taille max: 20 Mo.</div>
                        @error('file')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-700">Résumé (optionnel)</label>
                        <textarea wire:model.defer="form.doc_summary" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm"
                            rows="3"></textarea>
                        @error('form.doc_summary')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                        @enderror
                    </div>

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
