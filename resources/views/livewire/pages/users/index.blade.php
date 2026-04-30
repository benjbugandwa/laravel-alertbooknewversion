<div class="space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <div class="text-2xl font-bold">Utilisateurs</div>
            <div class="text-sm text-gray-600">
                Réservé aux superadmins : activation, rôles, organisation, province, désactivation.
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <x-ui-card>
        <div class="flex flex-wrap gap-2">
            <button type="button" class="px-3 py-2 rounded-lg text-sm border"
                :class="@js($tab) === 'pending' ? 'bg-gray-900 text-white border-gray-900' :
                    'bg-white text-gray-900 border-gray-200'"
                wire:click="setTab('pending')">
                En attente d’activation
                <span
                    class="ml-2 text-xs px-2 py-0.5 rounded-full {{ $tab === 'pending' ? 'bg-white/20' : 'bg-gray-100' }}">
                    {{ $counts['pending'] ?? 0 }}
                </span>
            </button>

            <button type="button" class="px-3 py-2 rounded-lg text-sm border"
                :class="@js($tab) === 'active' ? 'bg-gray-900 text-white border-gray-900' :
                    'bg-white text-gray-900 border-gray-200'"
                wire:click="setTab('active')">
                Actifs
                <span
                    class="ml-2 text-xs px-2 py-0.5 rounded-full {{ $tab === 'active' ? 'bg-white/20' : 'bg-gray-100' }}">
                    {{ $counts['active'] ?? 0 }}
                </span>
            </button>

            <button type="button" class="px-3 py-2 rounded-lg text-sm border"
                :class="@js($tab) === 'inactive' ? 'bg-gray-900 text-white border-gray-900' :
                    'bg-white text-gray-900 border-gray-200'"
                wire:click="setTab('inactive')">
                Inactifs
                <span
                    class="ml-2 text-xs px-2 py-0.5 rounded-full {{ $tab === 'inactive' ? 'bg-white/20' : 'bg-gray-100' }}">
                    {{ $counts['inactive'] ?? 0 }}
                </span>
            </button>

            <button type="button" class="px-3 py-2 rounded-lg text-sm border"
                :class="@js($tab) === 'all' ? 'bg-gray-900 text-white border-gray-900' :
                    'bg-white text-gray-900 border-gray-200'"
                wire:click="setTab('all')">
                Tous
            </button>
        </div>

        <div class="mt-4 grid grid-cols-1 lg:grid-cols-3 gap-4">
            <x-ui-input label="Recherche" placeholder="Nom, email, organisation…" wire:model.live="q" />

            <div class="space-y-1">
                <label class="text-sm font-medium text-gray-700">Organisation</label>
                <select wire:model.live="orgFilter"
                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white">
                    <option value="">Toutes</option>
                    @foreach ($organisations as $org)
                        <option value="{{ $org->id }}">{{ $org->org_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1">
                <label class="text-sm font-medium text-gray-700">Province</label>
                <select wire:model.live="provinceFilter"
                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white">
                    <option value="">Toutes</option>
                    @foreach ($provinces as $p)
                        <option value="{{ $p->code_province }}">{{ $p->nom_province }} ({{ $p->code_province }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mt-3 text-xs text-gray-500">
            Recherche sur <b>nom</b>, <b>email</b> et <b>organisation</b>.
        </div>
    </x-ui-card>

    <x-ui-table :headers="['Nom', 'Email', 'Organisation', 'Rôle', 'Province', 'Statut', 'Actions']">
        @forelse($users as $u)
            <tr>
                <td class="px-4 py-3 font-medium">{{ $u->name }}</td>
                <td class="px-4 py-3">{{ $u->email }}</td>
                <td class="px-4 py-3">{{ $u->organisation->org_name ?? '-' }}</td>
                <td class="px-4 py-3">{{ $u->roles->first()->name ?? '-' }}</td>
                <td class="px-4 py-3">
                    @if ($u->code_province)
                        {{ $u->code_province }}
                    @else
                        <span class="text-gray-500">-</span>
                    @endif
                </td>
                <td class="px-4 py-3">
                    @if ($u->is_active)
                        <span class="inline-flex px-2 py-1 text-xs rounded bg-green-100 text-green-800">Actif</span>
                    @else
                        <span class="inline-flex px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-800">Inactif</span>
                    @endif
                </td>
                <td class="px-4 py-3">
                    <div class="flex flex-wrap gap-2">
                        <x-ui-button size="sm" variant="secondary" wire:click="openAssign({{ $u->id }})">
                            Activer / Assigner
                        </x-ui-button>

                        <x-ui-button size="sm" variant="ghost" wire:click="toggleActive({{ $u->id }})">
                            {{ $u->is_active ? 'Désactiver' : 'Activer' }}
                        </x-ui-button>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td class="px-4 py-6 text-center text-gray-600" colspan="7">
                    Aucun utilisateur trouvé.
                </td>
            </tr>
        @endforelse
    </x-ui-table>

    <div>
        {{ $users->links() }}
    </div>

    {{-- Modal: Activer / Assigner --}}
    @if ($showAssignModal)
        <div class="fixed inset-0 z-50">
            <div class="absolute inset-0 bg-black/50" wire:click="$set('showAssignModal', false)"></div>

            <div class="absolute inset-0 flex items-center justify-center p-4">
                <div class="w-full max-w-lg bg-white rounded-2xl shadow-xl border">
                    <div class="px-5 py-4 border-b flex items-center justify-between">
                        <div class="font-semibold">Activer & Assigner</div>
                        <button class="opacity-60 hover:opacity-100"
                            wire:click="$set('showAssignModal', false)">✕</button>
                    </div>

                    <div class="p-5 space-y-4">
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-gray-700">Rôle</label>
                            <select wire:model="role_id"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white">
                                <option value="">-- choisir --</option>
                                @foreach ($roles as $r)
                                    <option value="{{ $r->id }}">{{ $r->name }}</option>
                                @endforeach
                            </select>
                            @error('role_id')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="text-sm font-medium text-gray-700">Province</label>
                            <select wire:model="code_province"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white">
                                <option value="">-- choisir --</option>
                                @foreach ($provinces as $p)
                                    <option value="{{ $p->code_province }}">{{ $p->nom_province }}
                                        ({{ $p->code_province }})</option>
                                @endforeach
                            </select>
                            @error('code_province')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end gap-2 pt-2">
                            <x-ui-button variant="secondary" wire:click="$set('showAssignModal', false)">
                                Annuler
                            </x-ui-button>

                            <x-ui-button wire:click="assignAndActivate" wire:loading.attr="disabled">
                                <span wire:loading.remove>Valider</span>
                                <span wire:loading>Traitement…</span>
                            </x-ui-button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal: Créer organisation --}}
    @if ($showCreateOrgModal)
        <div class="fixed inset-0 z-50">
            <div class="absolute inset-0 bg-black/50" wire:click="$set('showCreateOrgModal', false)"></div>

            <div class="absolute inset-0 flex items-center justify-center p-4">
                <div class="w-full max-w-lg bg-white rounded-2xl shadow-xl border">
                    <div class="px-5 py-4 border-b flex items-center justify-between">
                        <div class="font-semibold">Créer une organisation</div>
                        <button class="opacity-60 hover:opacity-100"
                            wire:click="$set('showCreateOrgModal', false)">✕</button>
                    </div>

                    <div class="p-5 space-y-4">
                        <x-ui-input label="Sigle" wire:model.defer="org_sigle" name="org_sigle"
                            placeholder="ex: HCR" />
                        <x-ui-input label="Nom de l’organisation" wire:model.defer="org_name" name="org_name"
                            placeholder="ex: UNHCR" />
                        <x-ui-input label="Secteur d’activité" wire:model.defer="org_secteur_activite"
                            name="org_secteur_activite" placeholder="ex: Protection" />
                        <x-ui-input label="Catégorie" wire:model.defer="org_categorie" name="org_categorie"
                            placeholder="ex: ONG / ONU" />

                        <div class="flex justify-end gap-2 pt-2">
                            <x-ui-button variant="secondary" wire:click="$set('showCreateOrgModal', false)">
                                Annuler
                            </x-ui-button>

                            <x-ui-button wire:click="createOrganisation" wire:loading.attr="disabled">
                                <span wire:loading.remove>Créer</span>
                                <span wire:loading>Création…</span>
                            </x-ui-button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
