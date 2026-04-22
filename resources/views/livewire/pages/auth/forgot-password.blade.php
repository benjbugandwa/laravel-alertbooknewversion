<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public string $email = '';

    public function sendPasswordResetLink(): void
    {
        $this->validate(
            [
                'email' => ['required', 'string', 'email'],
            ],
            [
                'email.required' => 'L’adresse e-mail est obligatoire.',
                'email.email' => 'Veuillez saisir une adresse e-mail valide.',
            ],
        );

        $status = Password::sendResetLink(['email' => $this->email]);

        if ($status === Password::RESET_LINK_SENT) {
            session()->flash('success', 'Un lien de réinitialisation a été envoyé à votre adresse e-mail.');
        } else {
            session()->flash('error', 'Impossible d’envoyer le lien. Vérifiez l’adresse e-mail.');
        }
    }
};
?>

<div class="space-y-6">
    <div>
        <div class="text-2xl font-bold">Mot de passe oublié</div>
        <div class="text-sm text-gray-600">
            Entrez votre adresse e-mail et nous vous enverrons un lien de réinitialisation.
        </div>
    </div>

    <x-ui-card>
        <form wire:submit="sendPasswordResetLink" class="space-y-4">
            <x-ui-input label="Adresse e-mail" type="email" wire:model.defer="email" name="email"
                placeholder="ex: nom@organisation.org" />

            <x-ui-button type="submit" class="w-full" wire:loading.attr="disabled">
                <span wire:loading.remove>Envoyer le lien</span>
                <span wire:loading>Envoi...</span>
            </x-ui-button>
        </form>
    </x-ui-card>

    <div class="text-sm text-gray-600 text-center">
        <a class="text-gray-900 font-medium hover:underline" href="{{ route('login') }}" wire:navigate>
            Retour à la connexion
        </a>
    </div>
</div>
