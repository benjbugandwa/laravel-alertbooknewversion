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
            ],
            [
                'name.required' => 'Le nom est obligatoire.',
                'email.required' => 'L’adresse e-mail est obligatoire.',
                'email.email' => 'Veuillez saisir une adresse e-mail valide.',
                'email.unique' => 'Cette adresse e-mail est déjà utilisée.',
                'password.required' => 'Le mot de passe est obligatoire.',
                'password.confirmed' => 'Les mots de passe ne correspondent pas.',
            ],
        );

        $user = \App\Models\User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),

            // ✅ logique GBV : en attente d'activation
            'is_active' => false,
            'org_id' => null,
            'user_role' => null,
            // 'code_province' => null, // ou demandée au signup si tu veux
        ]);

        // Optionnel : garde l’event Registered si tu veux la vérif email plus tard
        event(new \Illuminate\Auth\Events\Registered($user));

        // ✅ IMPORTANT : ne pas faire Auth::login($user)

        // ✅ notifier les superadmins par email
        \Illuminate\Support\Facades\Notification::send($this->superAdmins(), new \App\Notifications\NewAccountPendingActivationNotification($user));

        // ✅ message à l'utilisateur
        session()->flash('success', 'Compte créé avec succès. Un administrateur va l’activer avant votre première connexion.');

        // ✅ redirection login
        $this->redirect(route('login', absolute: false), navigate: true);
    }

    /**
     * Récupère les superadmins (pivot roles_users + roles.slug)
     */
    protected function superAdmins()
    {
        return \App\Models\User::query()
            ->whereHas('roles', fn($q) => $q->where('slug', 'superadmin'))
            ->where('is_active', true) // optionnel : éviter d’écrire aux superadmins inactifs
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
