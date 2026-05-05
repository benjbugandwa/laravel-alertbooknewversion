<?php

namespace App\Livewire\Pages\Users;

use App\Models\Organisation;
use App\Models\Province;
use App\Models\Role;
use App\Models\User;
use App\Notifications\AccountActivatedNotification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    // Tabs: all | pending | active | inactive
    public string $tab = 'pending';

    // Filters
    public string $q = '';
    public ?int $orgFilter = null;
    public ?string $provinceFilter = null;

    // Modals
    public bool $showAssignModal = false;
    public bool $showCreateOrgModal = false;

    // Selected user
    public ?int $selectedUserId = null;

    // Assign/Activate form
    public ?int $org_id = null;
    public ?int $role_id = null;
    public ?string $code_province = null;

    // Create organisation form
    public string $org_sigle = '';
    public string $org_name = '';
    public string $org_secteur_activite = '';
    public string $org_categorie = '';

    public function mount(): void
    {
        // Page réservée aux superadmins
        if (!Auth::user()?->isSuperAdmin()) {
            abort(403);
        }
    }

    public function setTab(string $tab): void
    {
        $allowed = ['all', 'pending', 'active', 'inactive'];
        $this->tab = in_array($tab, $allowed, true) ? $tab : 'pending';
        $this->resetPage();
    }

    public function updatingQ(): void
    {
        $this->resetPage();
    }
    public function updatingOrgFilter(): void
    {
        $this->resetPage();
    }
    public function updatingProvinceFilter(): void
    {
        $this->resetPage();
    }

    public function openAssign(int $userId): void
    {
        $this->resetValidation();
        $this->selectedUserId = $userId;

        $u = User::with('roles')->findOrFail($userId);

        $this->org_id = $u->org_id;
        $this->code_province = $u->code_province;
        $this->role_id = $u->roles->first()?->id;

        $this->showAssignModal = true;
    }

    public function assignAndActivate(): void
    {
        if (!Auth::user()?->isSuperAdmin()) abort(403);

        $this->validate([
            'role_id' => ['required', 'exists:roles,id'],
        ], [
            'role_id.required' => 'Veuillez sélectionner un rôle.',
        ]);

        $u = User::findOrFail($this->selectedUserId);

        $role = Role::findOrFail($this->role_id);
        $org  = $u->organisation;

        $wasInactive = ($u->is_active === false);

        $u->is_active = true;

        // Optionnel: garder user_role en synchro
        $u->user_role = $role->slug;

        $u->save();

        // Pivot roles_users = source de vérité
        $u->roles()->sync([$role->id]);

        // Email à l'utilisateur seulement si activation (inactif -> actif)
        if ($wasInactive) {
            $u->notify(new AccountActivatedNotification($org, $role, $u->code_province));
        }

        $this->showAssignModal = false;

        $this->dispatch('toast', message: "Utilisateur mis à jour : {$u->email}", type: 'success', duration: 5000);
    }

    public function toggleActive(int $userId): void
    {
        if (!Auth::user()?->isSuperAdmin()) abort(403);

        $u = User::findOrFail($userId);

        // Sécurité: éviter de se désactiver soi-même
        if ($u->id === Auth::id()) {
            $this->dispatch('toast', message: "Action non autorisée sur votre propre compte.", type: 'warning', duration: 6000);
            return;
        }

        $u->is_active = !$u->is_active;
        $u->save();

        $this->dispatch('toast', message: 'Statut utilisateur mis à jour.', type: 'success', duration: 5000);
    }

    public function openCreateOrg(): void
    {
        $this->resetValidation();

        $this->org_sigle = '';
        $this->org_name = '';
        $this->org_secteur_activite = '';
        $this->org_categorie = '';

        $this->showCreateOrgModal = true;
    }

    public function createOrganisation(): void
    {
        if (!Auth::user()?->isSuperAdmin()) abort(403);

        $this->validate([
            'org_name' => ['required', 'string', 'max:150'],
            'org_sigle' => ['nullable', 'string', 'max:100'],
            'org_secteur_activite' => ['nullable', 'string', 'max:50'],
            'org_categorie' => ['nullable', 'string', 'max:50'],
        ], [
            'org_name.required' => "Le nom de l'organisation est obligatoire.",
        ]);

        $org = Organisation::create([
            'org_sigle' => $this->org_sigle ?: null,
            'org_name' => $this->org_name,
            'org_secteur_activite' => $this->org_secteur_activite ?: null,
            'org_categorie' => $this->org_categorie ?: null,
        ]);

        // Pré-sélection dans le modal d’assignation
        $this->org_id = $org->id;

        $this->showCreateOrgModal = false;

        $this->dispatch('toast', message: "Organisation créée : {$org->org_name}", type: 'success', duration: 5000);
    }

    public function render()
    {
        $query = User::query()->with(['roles', 'organisation']);

        // Tabs
        if ($this->tab === 'pending') {
            // En attente = inactif (tu peux renforcer: org_id null ou role absent)
            $query->where('is_active', false);
        } elseif ($this->tab === 'active') {
            $query->where('is_active', true);
        } elseif ($this->tab === 'inactive') {
            $query->where('is_active', false);
        }

        // Filters
        if ($this->orgFilter) {
            $query->where('org_id', $this->orgFilter);
        }

        if ($this->provinceFilter) {
            $query->where('code_province', $this->provinceFilter);
        }

        // Search: name/email/organisation
        if (trim($this->q) !== '') {
            $s = '%' . trim($this->q) . '%';

            $query->where(function ($qq) use ($s) {
                $qq->where('name', 'ilike', $s)
                    ->orWhere('email', 'ilike', $s)
                    ->orWhereHas('organisation', fn($oq) => $oq->where('org_name', 'ilike', $s));
            });
        }

        // Counts for tabs
        $pendingCount  = User::where('is_active', false)->count();
        $activeCount   = User::where('is_active', true)->count();
        $inactiveCount = User::where('is_active', false)->count(); // même que pending si tu n’as pas une autre logique

        return view('livewire.pages.users.index', [
            'users' => $query->orderByDesc('id')->paginate(10),
            'roles' => Role::orderBy('name')->get(),
            'organisations' => Organisation::orderBy('org_name')->get(),
            'provinces' => Province::orderBy('nom_province')->get(),
            'counts' => [
                'pending' => $pendingCount,
                'active' => $activeCount,
                'inactive' => $inactiveCount,
            ],
        ]);
    }
}
