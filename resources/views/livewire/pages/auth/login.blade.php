<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    public function authenticate(): void
    {
        $this->validate(
            [
                'email' => ['required', 'string', 'email'],
                'password' => ['required', 'string'],
            ],
            [
                'email.required' => 'L’adresse e-mail est obligatoire.',
                'email.email' => 'Veuillez saisir une adresse e-mail valide.',
                'password.required' => 'Le mot de passe est obligatoire.',
            ],
        );

        if (!Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            throw ValidationException::withMessages([
                'email' => 'Identifiants incorrects.',
            ]);
        }

        // ✅ Bloquer les comptes inactifs
        if (Auth::user()->is_active === false) {
            Auth::logout();

            throw ValidationException::withMessages([
                'email' => 'Votre compte n’est pas encore activé. Veuillez contacter un administrateur.',
            ]);
        }

        request()->session()->regenerate();

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
};
?>

<div class="space-y-6">
    <div>
        <div class="text-2xl font-bold">Connexion</div>
        <div class="text-sm text-gray-600">
            Connectez-vous pour accéder au tableau de bord.
        </div>
    </div>

    <x-ui-card>
        <form wire:submit="authenticate" class="space-y-4">
            <x-ui-input label="Adresse e-mail" type="email" wire:model.defer="email" name="email"
                placeholder="ex: nom@organisation.org" autocomplete="username" autofocus />

            <x-ui-input label="Mot de passe" type="password" wire:model.defer="password" name="password"
                placeholder="••••••••" autocomplete="current-password" />

            <div class="flex items-center justify-between">
                <label class="inline-flex items-center gap-2 text-sm">
                    <input type="checkbox" class="rounded border-gray-300" wire:model="remember">
                    Se souvenir de moi
                </label>

                <a class="text-sm text-gray-700 hover:underline" href="{{ route('password.request') }}" wire:navigate>
                    Mot de passe oublié ?
                </a>
            </div>

            <x-ui-button type="submit" class="w-full" wire:loading.attr="disabled">
                <span wire:loading.remove>Se connecter</span>
                <span wire:loading>Connexion...</span>
            </x-ui-button>
        </form>
    </x-ui-card>

    <div class="text-sm text-gray-600 text-center">
        Pas encore de compte ?
        <a class="text-gray-900 font-medium hover:underline" href="{{ route('register') }}" wire:navigate>
            Créer un compte
        </a>
    </div>
</div>
