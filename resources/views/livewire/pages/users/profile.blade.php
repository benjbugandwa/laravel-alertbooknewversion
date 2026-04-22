<div class="space-y-6">
    <div>
        <div class="text-2xl font-bold">Mon profil</div>
        <div class="text-sm text-gray-600">Mettez à jour vos informations personnelles.</div>
    </div>

    <x-ui-card>
        @php($u = auth()->user())

        <div class="flex items-center gap-4">
            <div
                class="h-14 w-14 rounded-full bg-gray-200 overflow-hidden grid place-items-center text-sm font-semibold text-gray-700">
                @if ($u->avatar_url)
                    {{-- avatar_url peut être un chemin local (avatars/...) --}}
                    <img src="{{ str_starts_with($u->avatar_url, 'http') ? $u->avatar_url : asset('storage/' . $u->avatar_url) }}"
                        class="h-full w-full object-cover" alt="Avatar">
                @else
                    {{ strtoupper(substr($u->name ?? 'GB', 0, 2)) }}
                @endif
            </div>

            <div class="flex-1">
                <div class="font-semibold">{{ $u->name }}</div>
                <div class="text-sm text-gray-600">{{ $u->email }}</div>
                <div class="text-xs text-gray-500 mt-1">
                    Province: {{ $u->code_province ?? '-' }}
                </div>
            </div>
        </div>
    </x-ui-card>

    <x-ui-card>
        <form wire:submit="save" class="space-y-4">
            <x-ui-input label="Nom complet" wire:model.defer="name" name="name" />
            <x-ui-input label="Email" type="email" wire:model.defer="email" name="email" />
            <x-ui-input label="Téléphone" wire:model.defer="phone_number" name="phone_number" placeholder="+243..." />
            <x-ui-input label="Fonction" wire:model.defer="job_title" name="job_title" placeholder="ex: Data Manager" />

            <div class="space-y-1">
                <label class="text-sm font-medium text-gray-700">Photo de profil</label>
                <input type="file" wire:model="avatar" class="block w-full text-sm">
                <div class="text-xs text-gray-500">JPG/PNG — 2MB max.</div>
                @error('avatar')
                    <div class="text-sm text-red-600">{{ $message }}</div>
                @enderror
            </div>

            @if ($avatar)
                <div class="flex items-center gap-3">
                    <div class="h-14 w-14 rounded-full overflow-hidden border">
                        <img src="{{ $avatar->temporaryUrl() }}" class="h-full w-full object-cover" alt="Preview">
                    </div>
                    <div class="text-sm text-gray-600">Prévisualisation</div>
                </div>
            @endif

            <x-ui-button type="submit" wire:loading.attr="disabled">
                <span wire:loading.remove>Enregistrer</span>
                <span wire:loading>Enregistrement...</span>
            </x-ui-button>
        </form>
    </x-ui-card>
</div>
