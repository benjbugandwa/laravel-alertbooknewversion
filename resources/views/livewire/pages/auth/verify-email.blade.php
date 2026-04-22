<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new {#[Layout('components.layouts.guest')] class extends Component {
    public function sendVerification(): void
    {
        if (Auth::user()->hasVerifiedEmail()) {
            $this->redirect(route('dashboard', absolute: false), navigate: true);
            return;
        }

        Auth::user()->sendEmailVerificationNotification();
        session()->flash('success', 'Un nouveau lien de vérification a été envoyé.');
    }

    public function logout(): void
    {
        Auth::logout();
        Session::invalidate();
        Session::regenerateToken();

        $this->redirect(route('login', absolute: false), navigate: true);
    }
};
?>

<div class="space-y-6">
    <div>
        <div class="text-2xl font-bold">Vérifier votre adresse e-mail</div>
        <div class="text-sm text-gray-600">
            Avant de continuer, veuillez vérifier votre e-mail et cliquer sur le lien de confirmation.
            Si vous n’avez pas reçu l’e-mail, vous pouvez en demander un nouveau.
        </div>
    </div>

    <x-ui-card>
        <div class="space-y-4">
            <x-ui-button class="w-full" wire:click="sendVerification" wire:loading.attr="disabled">
                <span wire:loading.remove>Renvoyer l’e-mail de vérification</span>
                <span wire:loading>Envoi...</span>
            </x-ui-button>

            <x-ui-button variant="secondary" class="w-full" wire:click="logout">
                Se déconnecter
            </x-ui-button>
        </div>
    </x-ui-card>
</div>
