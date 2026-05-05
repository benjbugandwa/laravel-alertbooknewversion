<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public $org_id = null;
    public string $code_province = '';
    public $organisations = [];
    public $provinces = [];

    public function mount()
    {
        $this->organisations = \App\Models\Organisation::orderBy('org_name')->get();
        $this->provinces = \App\Models\Province::where('is_active', 'YES')->orderBy('nom_province')->get();
    }

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate(
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . \App\Models\User::class],
                'password' => ['required', 'string', \Illuminate\Validation\Rules\Password::defaults(), 'confirmed'],
                'org_id' => ['required', 'exists:organisations,id'],
                'code_province' => ['required', 'exists:provinces,code_province'],
            ],
            [
                'name.required' => 'Le nom est obligatoire.',
                'email.required' => 'L’adresse e-mail est obligatoire.',
                'email.email' => 'Veuillez saisir une adresse e-mail valide.',
                'email.unique' => 'Cette adresse e-mail est déjà utilisée.',
                'password.required' => 'Le mot de passe est obligatoire.',
                'password.confirmed' => 'Les mots de passe ne correspondent pas.',
                'org_id.required' => 'L’organisation est obligatoire.',
                'org_id.exists' => 'L’organisation sélectionnée est invalide.',
                'code_province.required' => 'La province est obligatoire.',
                'code_province.exists' => 'La province sélectionnée est invalide.',
            ],
        );

        $user = \App\Models\User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),

            // ✅ logique GBV : en attente d'activation
            'is_active' => false,
            'org_id' => $validated['org_id'],
            'code_province' => $validated['code_province'],
            'user_role' => null,
        ]);

        // Optionnel : garde l’event Registered si tu veux la vérif email plus tard
        event(new \Illuminate\Auth\Events\Registered($user));

        // ✅ IMPORTANT : ne pas faire Auth::login($user)

        // ✅ notifier les superadmins par email (filtrés par province)
        \Illuminate\Support\Facades\Notification::send($this->superAdmins($user->code_province), new \App\Notifications\NewAccountPendingActivationNotification($user));

        // ✅ message à l'utilisateur
        session()->flash('success', 'Compte créé avec succès. Un administrateur va l’activer avant votre première connexion.');

        // ✅ redirection login
        $this->redirect(route('login', absolute: false), navigate: true);
    }

    /**
     * Récupère les superadmins (pivot roles_users + roles.slug) filtrés par province
     */
    protected function superAdmins(string $codeProvince)
    {
        return \App\Models\User::query()
            ->whereHas('roles', fn($q) => $q->where('slug', 'superadmin'))
            ->where('code_province', $codeProvince)
            ->where('is_active', true)
            ->get();
    }
}; ?>

<div>
    <div class="space-y-6">
        <div>
            <div class="text-2xl font-bold">Créer un compte</div>
            <div class="text-sm text-gray-600">
                Renseignez vos informations pour commencer.
            </div>
        </div>

        <x-ui-card>
            <form wire:submit="register" class="space-y-4">
                <x-ui-input label="Nom complet" wire:model.defer="name" name="name" placeholder="ex: Jean Dupont" />
                <x-ui-input label="Adresse e-mail" type="email" wire:model.defer="email" name="email"
                    placeholder="ex: nom@organisation.org" />

                <x-ui-input label="Mot de passe" type="password" wire:model.defer="password" name="password"
                    placeholder="••••••••" />
                <x-ui-input label="Confirmer le mot de passe" type="password" wire:model.defer="password_confirmation"
                    name="password_confirmation" placeholder="••••••••" />

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-700">Province</label>
                        <select wire:model.defer="code_province" name="code_province"
                            class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-[#0B4F8A] focus:border-[#0B4F8A]">
                            <option value="">-- choisir --</option>
                            @foreach ($provinces as $p)
                                <option value="{{ $p->code_province }}">{{ $p->nom_province }}</option>
                            @endforeach
                        </select>
                        @error('code_province')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-700">Organisation</label>
                        <select wire:model.defer="org_id" name="org_id"
                            class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-[#0B4F8A] focus:border-[#0B4F8A]">
                            <option value="">-- choisir --</option>
                            @foreach ($organisations as $org)
                                <option value="{{ $org->id }}">{{ $org->org_name }}</option>
                            @endforeach
                        </select>
                        @error('org_id')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <x-ui-button type="submit" class="w-full" wire:loading.attr="disabled">
                    <span wire:loading.remove>Créer mon compte</span>
                    <span wire:loading>Création...</span>
                </x-ui-button>
            </form>
        </x-ui-card>

        <div class="text-sm text-gray-600 text-center">
            Déjà un compte ?
            <a class="text-gray-900 font-medium hover:underline" href="{{ route('login') }}" wire:navigate>
                Se connecter
            </a>
        </div>
    </div>
</div>
