<?php

namespace App\Livewire\Pages\Users;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class Profile extends Component
{
    use WithFileUploads;

    public string $name = '';
    public string $email = '';
    public ?string $phone_number = null; // ✅ SQL
    public ?string $job_title = null;    // ✅ ajout
    public $avatar; // TemporaryUploadedFile|null

    public function mount(): void
    {
        $u = Auth::user();

        $this->name = (string) $u->name;
        $this->email = (string) $u->email;
        $this->phone_number = $u->phone_number; // ✅ SQL
        $this->job_title = $u->job_title;       // ✅ ajout
    }

    public function save(): void
    {
        $u = Auth::user();

        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($u->id)],
            'phone_number' => ['nullable', 'string', 'max:30'], // ✅ SQL
            'job_title' => ['nullable', 'string', 'max:120'],   // ✅ ajout
            'avatar' => ['nullable', 'image', 'max:2048'],      // 2MB
        ], [
            'avatar.image' => 'Le fichier doit être une image.',
            'avatar.max' => 'La photo ne doit pas dépasser 2MB.',
        ]);

        $u->name = $this->name;
        $u->email = $this->email;
        $u->phone_number = $this->phone_number; // ✅ SQL
        $u->job_title = $this->job_title;       // ✅ ajout

        if ($this->avatar) {
            // supprimer ancien avatar si on stockait déjà un chemin local
            if ($u->avatar_url && str_starts_with($u->avatar_url, 'avatars/') && \Storage::disk('public')->exists($u->avatar_url)) {
                \Storage::disk('public')->delete($u->avatar_url);
            }

            // stocker dans storage/app/public/avatars
            $path = $this->avatar->store('avatars', 'public');

            // ✅ IMPORTANT : on écrit dans avatar_url (nom exact SQL)
            $u->avatar_url = $path;
        }

        $u->save();

        $this->dispatch('toast', message: 'Profil mis à jour.', type: 'success', duration: 5000);
    }

    public function render()
    {
        return view('livewire.pages.users.profile');
    }
}
