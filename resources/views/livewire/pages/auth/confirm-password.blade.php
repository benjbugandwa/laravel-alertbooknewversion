<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public string $password = '';

    public function confirmPassword(): void
    {
        $this->validate(
            [
                'password' => ['required', 'string'],
            ],
            [
                'password.required' => 'Veuillez saisir votre mot de passe.',
            ],
        );

        if (!Hash::check($this->password, Auth::user()->password)) {
            $this->addError('password', 'Mot de passe incorrect.');
            return;
        }

        session(['auth.password_confirmed_at' => time()]);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
};
?>

<div class="space-y-6">
    <div>
        <div class="text-2xl font-bold">Confirmer le mot de passe</div>
        <div class="text-sm text-gray-600">
            Pour continuer, veuillez confirmer votre mot de passe.
        </div>
    </div>

    <x-ui-card>
        <form wire:submit="confirmPassword" class="space-y-4">
            <x-ui-input label="Mot de passe" type="password" wire:model.defer="password" name="password" />

            <x-ui-button type="submit" class="w-full" wire:loading.attr="disabled">
                <span wire:loading.remove>Confirmer</span>
                <span wire:loading>Vérification...</span>
            </x-ui-button>
        </form>
    </x-ui-card>
</div>
