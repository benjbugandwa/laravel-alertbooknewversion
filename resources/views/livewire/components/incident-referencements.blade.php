<div class="space-y-4">
    {{-- Header --}}
    <x-ui-card>
        <div class="flex items-center justify-between gap-4">
            <div>
                <div class="text-sm font-semibold">Référencements</div>
                <div class="text-xs text-gray-500">Orientation vers structures de prise en charge.</div>
            </div>

            @if (in_array(auth()->user()->user_role, ['superadmin', 'admin', 'superviseur']))
                <x-ui-button wire:click="openCreate">
                    + Nouveau référencement
                </x-ui-button>
            @endif
        </div>
    </x-ui-card>

    {{-- Liste --}}
    <x-ui-card>
        @if ($this->referencements->isEmpty())
            <div class="text-sm text-gray-500">Aucun référencement enregistré.</div>
        @else
            <div class="space-y-4">
                @foreach ($this->referencements as $r)
                    @php
                        $st = $r->statut_reponse ?? 'En attente';
                        $badge = match ($st) {
                            'En attente' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                            'En cours' => 'bg-blue-100 text-blue-800 border-blue-200',
                            'Fournie' => 'bg-green-100 text-green-800 border-green-200',
                            'refusée' => 'bg-red-100 text-red-800 border-red-200',
                            default => 'bg-gray-100 text-gray-700 border-gray-200',
                        };
                    @endphp

                    <div class="border rounded-2xl p-4">
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <div class="font-semibold text-sm">
                                        {{ $r->code_referencement }}
                                    </div>
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 text-xs rounded-full border {{ $badge }}">
                                        {{ $st }}
                                    </span>
                                </div>

                                <div class="text-sm text-gray-700 mt-1">
                                    <span class="font-medium">{{ $r->provider->provider_name ?? '-' }}</span>
                                    <span class="text-gray-500">—
                                        {{ is_array($r->provider->provider_location) ? implode(', ', $r->provider->provider_location) : $r->provider->provider_location ?? '-' }}</span>

                                </div>

                                <div class="text-xs text-gray-500 mt-1">
                                    {{ optional($r->date_referencement)->format('Y-m-d') }} •
                                    {{ $r->type_reponse }} •
                                    Ajouté par {{ $r->author->name ?? '—' }}
                                </div>
                            </div>

                            @if (in_array(auth()->user()->user_role, ['superadmin', 'admin', 'superviseur']))
                                <x-ui-button size="sm" variant="secondary"
                                    wire:click="openEdit('{{ $r->id }}')">
                                    Éditer
                                </x-ui-button>
                            @endif
                        </div>

                        @if ($r->resultat)
                            <div class="mt-3 text-sm text-gray-700 whitespace-pre-line">
                                <span class="font-medium">Résultat :</span> {{ $r->resultat }}
                            </div>
                        @endif

                        @if ($r->observations)
                            <div class="mt-2 text-sm text-gray-700 whitespace-pre-line">
                                <span class="font-medium">Observations :</span> {{ $r->observations }}
                            </div>
                        @endif

                        @if ($r->file_path)
                            <div class="mt-3">
                                <a href="{{ asset('storage/' . $r->file_path) }}" target="_blank"
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

    {{-- Modal Référencement --}}
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-data
            x-on:keydown.escape.window="$wire.set('showModal', false)">
            <div class="absolute inset-0 bg-black/50" wire:click="$set('showModal', false)"></div>

            <div class="relative w-full max-w-2xl bg-white rounded-2xl shadow-xl border max-h-[85vh] flex flex-col">
                <div class="px-5 py-4 border-b flex items-center justify-between shrink-0">
                    <div class="font-semibold">
                        {{ $editing ? 'Modifier le référencement' : 'Nouveau référencement' }}
                    </div>
                    <button class="opacity-60 hover:opacity-100" wire:click="$set('showModal', false)">✕</button>
                </div>

                <div class="p-5 space-y-4 overflow-y-auto">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-gray-700">Date *</label>
                            <input type="date" wire:model.defer="form.date_referencement"
                                max="{{ now()->toDateString() }}"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white">
                            @error('form.date_referencement')
                                <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="text-sm font-medium text-gray-700">Statut réponse *</label>
                            <select wire:model.defer="form.statut_reponse"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white">
                                @foreach ($this->statusOptions as $st)
                                    <option value="{{ $st }}">{{ $st }}</option>
                                @endforeach
                            </select>
                            @error('form.statut_reponse')
                                <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="space-y-1 md:col-span-2">
                            <label class="text-sm font-medium text-gray-700">Type de réponse *</label>
                            <select wire:model.live="form.type_reponse"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white">
                                <option value="">-- Sélectionner --</option>
                                @foreach ($this->typeOptions as $t)
                                    <option value="{{ $t }}">{{ $t }}</option>
                                @endforeach
                            </select>
                            @error('form.type_reponse')
                                <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="space-y-1 md:col-span-2">
                            <label class="text-sm font-medium text-gray-700">Structure *</label>
                            <select wire:model.defer="form.provider_id"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white"
                                {{ empty($form['type_reponse']) ? 'disabled' : '' }}>
                                <option value="">
                                    {{ empty($form['type_reponse']) ? '— Choisissez d’abord le type de réponse —' : '— Sélectionner —' }}
                                </option>
                                @foreach ($this->providersFiltered as $p)
                                    <option value="{{ $p->id }}">{{ $p->provider_name }} —

                                        {{ is_array($p->provider_location) ? implode(', ', $p->provider_location) : $p->provider_location ?? '-' }}
                                @endforeach
                            </select>
                            @error('form.provider_id')
                                <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror

                            @if (!empty($form['type_reponse']) && $this->providersFiltered->isEmpty())
                                <div
                                    class="rounded-xl border border-gray-200 bg-gray-50 p-3 text-sm text-gray-700 mt-2">
                                    <div class="font-medium">Aucun partenaire ne propose ce service pour le moment.
                                    </div>
                                    @if (auth()->user()->user_role === 'superadmin')
                                        <div class="mt-2">
                                            <button type="button"
                                                class="inline-flex items-center gap-2 text-sm font-medium text-gray-900 hover:underline"
                                                wire:click="openQuickProviderCreate">+ Créer une structure</button>
                                        </div>
                                    @else
                                        <div class="mt-1 text-xs text-gray-500">Contactez un superadmin pour ajouter une
                                            structure.</div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" class="rounded border-gray-300" wire:model.defer="form.besoin_suivi">
                        <span>Besoin de suivi</span>
                    </label>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-700">Résultat</label>
                        <textarea wire:model.defer="form.resultat" rows="3"
                            class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm"></textarea>
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-700">Observations</label>
                        <textarea wire:model.defer="form.observations" rows="3"
                            class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm"></textarea>
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-700">Pièce jointe (optionnel)</label>
                        <input type="file" wire:model="file" class="block w-full text-sm">
                        @error('file')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                        @enderror
                        <div class="text-xs text-gray-500">PDF/JPG/PNG — 4MB max.</div>
                    </div>
                </div>

                <div class="px-5 py-4 border-t bg-white flex items-center justify-between gap-3 shrink-0">
                    <div class="text-xs text-gray-500">
                        @if ($this->incidentStatus !== 'Validé')
                            <span class="text-red-600 font-medium">Incident non validé :</span> vous ne pouvez pas
                            enregistrer un référencement.
                        @else
                            Tous les champs marqués * sont obligatoires.
                        @endif
                    </div>
                    <div class="flex justify-end gap-2">
                        <x-ui-button variant="secondary" wire:click="$set('showModal', false)">Annuler</x-ui-button>
                        <x-ui-button wire:click="save" wire:loading.attr="disabled" :disabled="$this->incidentStatus !== 'Validé' || !in_array(auth()->user()->user_role, ['superadmin', 'admin', 'superviseur'])">
                            <span wire:loading.remove>{{ $editing ? 'Enregistrer' : 'Créer' }}</span>
                            <span wire:loading>Traitement…</span>
                        </x-ui-button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Quick Create Provider Modal --}}
    @if ($showQuickProviderModal)
        <div class="fixed inset-0 z-[60] flex items-center justify-center p-4" x-data
            x-on:keydown.escape.window="$wire.set('showQuickProviderModal', false)">
            <div class="absolute inset-0 bg-black/50" wire:click="$set('showQuickProviderModal', false)"></div>
            <div class="relative w-full max-w-2xl bg-white rounded-2xl shadow-xl border max-h-[85vh] flex flex-col">
                <div class="px-5 py-4 border-b flex items-center justify-between">
                    <div class="font-semibold">Nouvelle structure de prise en charge</div>
                    <button class="opacity-60 hover:opacity-100"
                        wire:click="$set('showQuickProviderModal', false)">✕</button>
                </div>
                <div class="p-5 space-y-4 overflow-y-auto">
                    <x-ui-input label="Nom *" wire:model.defer="providerForm.provider_name" />
                    @error('providerForm.provider_name')
                        <div class="text-sm text-red-600">{{ $message }}</div>
                    @enderror
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-ui-input label="Localisation" wire:model.defer="providerForm.provider_location" />
                        <x-ui-input label="Nom focal point" wire:model.defer="providerForm.focalpoint_name" />
                        <x-ui-input label="Email focal point" wire:model.defer="providerForm.focalpoint_email" />
                        <x-ui-input label="Téléphone focal point" wire:model.defer="providerForm.focalpoint_number" />
                    </div>
                    <div class="space-y-2">
                        <div class="text-sm font-medium text-gray-700">Services proposés</div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            @foreach ($this->typeOptions as $opt)
                                <label class="inline-flex items-center gap-2 text-sm">
                                    <input type="checkbox" class="rounded border-gray-300"
                                        wire:model.live="providerForm.type_services_proposes"
                                        value="{{ $opt }}">
                                    <span>{{ $opt }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="px-5 py-4 border-t flex justify-end gap-2">
                    <x-ui-button variant="secondary"
                        wire:click="$set('showQuickProviderModal', false)">Annuler</x-ui-button>
                    <x-ui-button wire:click="saveQuickProvider" wire:loading.attr="disabled">
                        <span wire:loading.remove>Créer</span>
                        <span wire:loading>Traitement…</span>
                    </x-ui-button>
                </div>
            </div>
        </div>
    @endif
</div> {{-- Balise FERMANTE ajoutée ici pour le div principal --}}
