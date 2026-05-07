<div class="space-y-6 relative">
    {{-- Loading Spinner --}}
    <div wire:loading
        class="absolute inset-0 z-[60] flex items-center justify-center bg-white/50 backdrop-blur-sm rounded-2xl">
        <div class="flex flex-col items-center gap-2">
            <svg class="animate-spin h-8 w-8 text-onu" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-sm font-medium text-gray-700">Chargement...</span>
        </div>
    </div>

    <div class="flex items-start justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Mouvements de population</h1>
            <p class="text-sm text-gray-500">Suivi des déplacements, fuites et retours de populations.</p>
        </div>
        <x-ui-button wire:click="openCreate">
            <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
            Nouveau déplacement
        </x-ui-button>
    </div>

    {{-- Filtres --}}
    <x-ui-card class="p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Recherche</label>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Localité, cause..." 
                    class="w-full text-sm border-gray-300 rounded-lg focus:ring-onu focus:border-onu">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Type</label>
                <select wire:model.live="f_type" class="w-full text-sm border-gray-300 rounded-lg">
                    <option value="">Tous les types</option>
                    <option value="Fuite">Fuite</option>
                    <option value="Retour">Retour</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Province d'accueil</label>
                <select wire:model.live="f_province" class="w-full text-sm border-gray-300 rounded-lg">
                    <option value="">Toutes les provinces</option>
                    @foreach($provinces as $p)
                        <option value="{{ $p['code_province'] }}">{{ $p['nom_province'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </x-ui-card>

    {{-- Liste --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-bold">
                <tr>
                    <th class="px-6 py-4">Date</th>
                    <th class="px-6 py-4">Type</th>
                    <th class="px-6 py-4">Provenance</th>
                    <th class="px-6 py-4">Accueil</th>
                    <th class="px-6 py-4 text-right">Population</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($mouvements as $m)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-6 py-4 whitespace-nowrap font-medium">
                            {{ $m->date_mouvement->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $m->type_mouvement === 'Fuite' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                                {{ $m->type_mouvement }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ $m->localite_prov }}</div>
                            <div class="text-xs text-gray-500">{{ $m->territoireProv->nom_territoire ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ $m->localite_accl }}</div>
                            <div class="text-xs text-gray-500">{{ $m->territoireAccl->nom_territoire ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="font-bold text-onu">{{ number_format($m->estim_nbre_personnes) }}</div>
                            <div class="text-[10px] text-gray-400">{{ number_format($m->estim_nbre_menages) }} ménages</div>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="{{ route('mouvements.print', $m->id) }}" title="Imprimer la fiche"
                                class="inline-flex items-center justify-center h-8 w-8 rounded-lg text-gray-400 hover:text-onu hover:bg-onu/5 transition">
                                <i data-lucide="printer" class="w-4 h-4"></i>
                            </a>
                            <button wire:click="openEdit({{ $m->id }})" class="inline-flex items-center justify-center h-8 w-8 rounded-lg text-gray-400 hover:text-onu hover:bg-onu/5 transition">
                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                            </button>
                            @if(auth()->user()->user_role === 'superadmin')
                                <button wire:confirm="Supprimer ce mouvement ?" wire:click="delete({{ $m->id }})" 
                                    class="inline-flex items-center justify-center h-8 w-8 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <i data-lucide="inbox" class="w-12 h-12 mx-auto mb-4 opacity-20"></i>
                            Aucun mouvement trouvé.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if($mouvements->hasPages())
            <div class="px-6 py-4 bg-gray-50 border-t">
                {{ $mouvements->links() }}
            </div>
        @endif
    </div>

    {{-- Modal Formulaire (Refactorisé pour meilleur défilement) --}}
    @if($showModal)
        <div class="fixed inset-0 z-[70] overflow-y-auto py-6 px-4 sm:px-0">
            <div class="flex items-center justify-center min-h-full">
                {{-- Backdrop --}}
                <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" wire:click="$set('showModal', false)"></div>

                {{-- Modal Container --}}
                <div class="relative bg-white w-full max-w-4xl rounded-2xl shadow-2xl border overflow-hidden flex flex-col my-auto" style="max-height: 90vh;">
                    {{-- Header --}}
                    <div class="shrink-0 bg-white border-b px-6 py-4 flex items-center justify-between">
                        <h2 class="text-xl font-bold text-gray-900">{{ $editing ? 'Modifier le mouvement' : 'Nouveau mouvement' }}</h2>
                        <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600 p-1">
                            <i data-lucide="x" class="w-6 h-6"></i>
                        </button>
                    </div>

                    {{-- Scrollable Content --}}
                    <div class="flex-1 overflow-y-auto p-6 space-y-8 bg-white">
                        {{-- Lien avec Alerte --}}
                        <div class="bg-onu/5 border border-onu/10 rounded-xl p-5">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-onu mb-1">Lier à une alerte (optionnel)</label>
                                    <select wire:model.live="form.incident_id" class="w-full border-onu/20 rounded-lg text-sm focus:ring-onu bg-white">
                                        <option value="">-- Aucun lien direct (Autre cause) --</option>
                                        @foreach($all_incidents as $inc)
                                            <option value="{{ $inc['id'] }}">[{{ $inc['code_incident'] }}] {{ $inc['localite'] }} ({{ $inc['date_incident'] }})</option>
                                        @endforeach
                                    </select>
                                    <p class="mt-1 text-[10px] text-onu/60 italic">Si lié, la cause sera héritée de l'alerte.</p>
                                </div>
                                @if(!$form->incident_id)
                                    <div>
                                        <label class="block text-sm font-semibold text-onu mb-1">Cause du déplacement *</label>
                                        <input type="text" wire:model="form.cause_deplacement" placeholder="Ex: Affrontements armés..."
                                            class="w-full border-onu/20 rounded-lg text-sm focus:ring-onu">
                                        @error('form.cause_deplacement') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Infos de base --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1 text-xs font-bold uppercase">Date du mouvement *</label>
                                <input type="date" wire:model="form.date_mouvement" max="{{ now()->toDateString() }}"
                                    class="w-full border-gray-300 rounded-lg text-sm shadow-sm">
                                @error('form.date_mouvement') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1 text-xs font-bold uppercase">Type de mouvement *</label>
                                <select wire:model="form.type_mouvement" class="w-full border-gray-300 rounded-lg text-sm shadow-sm">
                                    <option value="">Sélectionner...</option>
                                    <option value="Fuite">Fuite (Déplacement)</option>
                                    <option value="Retour">Retour</option>
                                </select>
                                @error('form.type_mouvement') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1 text-xs font-bold uppercase">Source d'information *</label>
                                <input type="text" wire:model="form.source_info" class="w-full border-gray-300 rounded-lg text-sm shadow-sm" placeholder="Ex: Chef de localité...">
                                @error('form.source_info') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- Geographie --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                            {{-- Provenance --}}
                            <div class="space-y-4">
                                <h3 class="font-bold text-gray-900 border-b-2 border-red-100 pb-2 flex items-center gap-2">
                                    <i data-lucide="map-pin" class="w-4 h-4 text-red-500"></i> Provenance
                                </h3>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-[10px] text-gray-400 font-bold uppercase mb-1">Province *</label>
                                        <select wire:model.live="form.code_province_prov" class="w-full border-gray-300 rounded-lg text-sm shadow-sm">
                                            <option value="">Choisir...</option>
                                            @foreach($provinces as $p)
                                                <option value="{{ $p['code_province'] }}">{{ $p['nom_province'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] text-gray-400 font-bold uppercase mb-1">Territoire *</label>
                                        <select wire:model.live="form.code_territoire_prov" class="w-full border-gray-300 rounded-lg text-sm shadow-sm">
                                            <option value="">Choisir...</option>
                                            @foreach($territoires_prov as $t)
                                                <option value="{{ $t['code_territoire'] }}">{{ $t['nom_territoire'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[10px] text-gray-400 font-bold uppercase mb-1">Zone de santé</label>
                                    <select wire:model.live="form.code_zonesante_prov" class="w-full border-gray-300 rounded-lg text-sm shadow-sm">
                                        <option value="">Choisir...</option>
                                        @foreach($zones_prov as $z)
                                            <option value="{{ $z['code_zonesante'] }}">{{ $z['nom_zonesante'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] text-gray-400 font-bold uppercase mb-1">Localité spécifique *</label>
                                    <input type="text" wire:model="form.localite_prov" class="w-full border-gray-300 rounded-lg text-sm shadow-sm" placeholder="Village, quartier...">
                                    @error('form.localite_prov') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            {{-- Accueil --}}
                            <div class="space-y-4">
                                <h3 class="font-bold text-gray-900 border-b-2 border-green-100 pb-2 flex items-center gap-2">
                                    <i data-lucide="home" class="w-4 h-4 text-green-500"></i> Lieu d'accueil
                                </h3>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-[10px] text-gray-400 font-bold uppercase mb-1">Province *</label>
                                        <select wire:model.live="form.code_province_accl" class="w-full border-gray-300 rounded-lg text-sm shadow-sm">
                                            <option value="">Choisir...</option>
                                            @foreach($provinces as $p)
                                                <option value="{{ $p['code_province'] }}">{{ $p['nom_province'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] text-gray-400 font-bold uppercase mb-1">Territoire *</label>
                                        <select wire:model.live="form.code_territoire_accl" class="w-full border-gray-300 rounded-lg text-sm shadow-sm">
                                            <option value="">Choisir...</option>
                                            @foreach($territoires_accl as $t)
                                                <option value="{{ $t['code_territoire'] }}">{{ $t['nom_territoire'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[10px] text-gray-400 font-bold uppercase mb-1">Zone de santé</label>
                                    <select wire:model.live="form.code_zonesante_accl" class="w-full border-gray-300 rounded-lg text-sm shadow-sm">
                                        <option value="">Choisir...</option>
                                        @foreach($zones_accl as $z)
                                            <option value="{{ $z['code_zonesante'] }}">{{ $z['nom_zonesante'] }}</option>
                                        @endforeach
                                    </select>
                                    @error('form.code_zonesante_accl') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-[10px] text-gray-400 font-bold uppercase mb-1">Localité spécifique *</label>
                                    <input type="text" wire:model="form.localite_accl" class="w-full border-gray-300 rounded-lg text-sm shadow-sm" placeholder="Village d'accueil...">
                                    @error('form.localite_accl') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Chiffres --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1 text-xs font-bold uppercase">Ménages estimatifs *</label>
                                <input type="number" wire:model.live="form.estim_nbre_menages" class="w-full border-gray-300 rounded-lg text-sm shadow-sm" min="1">
                                @error('form.estim_nbre_menages') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1 text-xs font-bold uppercase">Personnes estimatives *</label>
                                <input type="number" wire:model.live="form.estim_nbre_personnes" class="w-full border-gray-300 rounded-lg text-sm shadow-sm" min="1">
                                @error('form.estim_nbre_personnes') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1 text-xs font-bold uppercase">Type de logement</label>
                                <select wire:model="form.type_logement" class="w-full border-gray-300 rounded-lg text-sm shadow-sm">
                                    <option value="">Sélectionner...</option>
                                    <option value="Site spontané">Site spontané</option>
                                    <option value="Centre collectif">Centre collectif</option>
                                    <option value="Famille accueil">Famille d'accueil</option>
                                    <option value="Autre">Autre</option>
                                </select>
                                @error('form.type_logement') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1 text-xs font-bold uppercase">Remarques additionnelles</label>
                            <textarea wire:model="form.remarques_mouvement" rows="3" class="w-full border-gray-300 rounded-lg text-sm shadow-sm" placeholder="Précisez le contexte ou les besoins urgents..."></textarea>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="shrink-0 bg-gray-50 border-t px-6 py-4 flex justify-end gap-3">
                        <x-ui-button variant="secondary" wire:click="$set('showModal', false)">Annuler</x-ui-button>
                        <x-ui-button wire:click="save" wire:loading.attr="disabled">
                            <span wire:loading.remove>{{ $editing ? 'Mettre à jour' : 'Enregistrer le mouvement' }}</span>
                            <span wire:loading>Traitement...</span>
                        </x-ui-button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @push('scripts')
    <script>
        document.addEventListener('livewire:initialized', () => {
            lucide.createIcons();
        });
        document.addEventListener('livewire:navigated', () => {
            lucide.createIcons();
        });
    </script>
    @endpush
</div>
