<?php

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules\Password as PasswordRules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public string $token = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function mount(string $token): void
    {
        $this->token = $token;
        $this->email = request()->query('email', '');
    }

    public function resetPassword(): void
    {
        $this->validate(
            [
                'token' => ['required'],
                'email' => ['required', 'string', 'email'],
                'password' => ['required', 'string', PasswordRules::defaults(), 'confirmed'],
            ],
            [
                'email.required' => 'L’adresse e-mail est obligatoire.',
                'email.email' => 'Veuillez saisir une adresse e-mail valide.',
                'password.required' => 'Le mot de passe est obligatoire.',
                'password.confirmed' => 'Les mots de passe ne correspondent pas.',
            ],
        );

        $status = Password::reset(
            [
                'email' => $this->email,
                'password' => $this->password,
                'password_confirmation' => $this->password_confirmation,
                'token' => $this->token,
            ],
            function ($user) {
                $user
                    ->forceFill([
                        'password' => Hash::make($this->password),
                    ])
                    ->save();
            },
        );

        if ($status === Password::PASSWORD_RESET) {
            session()->flash('success', 'Mot de passe réinitialisé avec succès. Vous pouvez vous connecter.');
            $this->redirect(route('login', absolute: false), navigate: true);
        } else {
            session()->flash('error', 'Échec de la réinitialisation. Le lien est peut-être expiré.');
        }
    }
};
?>

<div class="space-y-6">
    <div>
        <div class="text-2xl font-bold">Réinitialiser le mot de passe</div>
        <div class="text-sm text-gray-600">
            Choisissez un nouveau mot de passe.
        </div>
    </div>

    <x-ui-card>
        <form wire:submit="resetPassword" class="space-y-4">
            <x-ui-input label="Adresse e-mail" type="email" wire:model.defer="email" name="email" />
            <x-ui-input label="Nouveau mot de passe" type="password" wire:model.defer="password" name="password" />
            <x-ui-input label="Confirmer le mot de passe" type="password" wire:model.defer="password_confirmation"
                name="password_confirmation" />

            <x-ui-button type="submit" class="w-full" wire:loading.attr="disabled">
                <span wire:loading.remove>Réinitialiser</span>
                <span wire:loading>Traitement...</span>
            </x-ui-button>
        </form>
    </x-ui-card>
</div>
