<?php

namespace App\Livewire\Pages\Incidents;

use App\Models\Incident;
use App\Models\Survivant;
use App\Services\IncidentService;
use App\Exceptions\BusinessRuleException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Livewire\Forms\IncidentForm;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination, WithFileUploads;

    /** Service */
    protected IncidentService $incidentService;

    // Filtres
    public string $q = '';
    public string $f_status = '';
    public string $f_severite = '';
    public string $f_province = '';
    public string $f_territoire = '';
    public string $f_zone = '';
    public string $f_evenement = '';
    public ?string $date_from = null;
    public ?string $date_to = null;

    public int $perPage = 10;

    // Modal create/edit
    public bool $showModal = false;
    public bool $editing = false;
    public ?string $editingId = null;

    // Modal assign
    public bool $showAssignModal = false;
    public ?string $assignIncidentId = null;
    public ?string $assignTo = null; // uuid superviseur (users.id = uuid)

    // Confirmation modal
    public bool $showConfirmModal = false;
    public string $confirmTitle = '';
    public string $confirmMessage = '';
    public string $confirmAction = ''; // confirmValidate | confirmArchive
    public ?string $confirmIncidentId = null;

    // Upload
    public $photo; // TemporaryUploadedFile|null

    // Form
    public IncidentForm $form;

    /** Livewire DI */
    public function boot(IncidentService $incidentService): void
    {
        $this->incidentService = $incidentService;
    }

    public function mount(): void
    {
        $this->bootstrapScopeAndLists();
    }

    /* -------------------------------------------
     | Helpers rôles / scopes
     ------------------------------------------- */
    private function user()
    {
        return Auth::user();
    }

    private function isSuperAdmin(): bool
    {
        return $this->user()?->user_role === 'superadmin';
    }

    private function isAdmin(): bool
    {
        return $this->user()?->user_role === 'admin';
    }

    private function isSuperviseur(): bool
    {
        return $this->user()?->user_role === 'superviseur';
    }

    private function isMoniteur(): bool
    {
        return $this->user()?->user_role === 'moniteur';
    }

    private function sameProvinceAsUser(Incident $incident): bool
    {
        $u = $this->user();
        return $u && $u->code_province && $incident->code_province === $u->code_province;
    }

    private function isLocked(Incident $incident): bool
    {
        return in_array($incident->statut_incident, ['Cloturée', 'Archivé'], true);
    }

    private function canEditIncident(Incident $incident): bool
    {
        if ($this->isMoniteur()) return false;
        if ($this->isLocked($incident)) return false;
        if (!$this->isSuperAdmin() && !$this->sameProvinceAsUser($incident)) return false;
        return true;
    }

    /* -------------------------------------------
     | Computed Lists (provinces/territoires/zones)
     ------------------------------------------- */
    private function bootstrapScopeAndLists(): void
    {
        if (!$this->isSuperAdmin()) {
            $this->f_province = $this->user()->code_province ?? '';
        }
    }

    #[Computed]
    public function provinces()
    {
        return \App\Models\Province::select('code_province', 'nom_province')
            ->orderBy('nom_province')
            ->get()
            ->map(fn($p) => ['code' => $p->code_province, 'name' => $p->nom_province])
            ->toArray();
    }

    #[Computed]
    public function territoires()
    {
        $codeProvince = $this->showModal ? $this->form->code_province : $this->f_province;

        if (!$codeProvince) return [];

        return DB::table('territoires')
            ->select('code_territoire', 'nom_territoire')
            ->where('code_province', $codeProvince)
            ->orderBy('nom_territoire')
            ->get()
            ->map(fn($t) => ['code' => $t->code_territoire, 'name' => $t->nom_territoire])
            ->toArray();
    }

    #[Computed]
    public function zones()
    {
        $codeTerritoire = $this->showModal ? $this->form->code_territoire : $this->f_territoire;

        if (!$codeTerritoire) return [];

        return DB::table('zonesantes')
            ->select('code_zonesante', 'nom_zonesante')
            ->where('code_territoire', $codeTerritoire)
            ->orderBy('nom_zonesante')
            ->get()
            ->map(fn($z) => ['code' => $z->code_zonesante, 'name' => $z->nom_zonesante])
            ->toArray();
    }

    #[Computed]
    public function chefferies()
    {
        $codeTerritoire = $this->showModal ? $this->form->code_territoire : $this->f_territoire;

        if (!$codeTerritoire) return [];

        return DB::table('chefferies')
            ->select('code_chefferie', 'nom_chefferie')
            ->where('code_territoire', $codeTerritoire)
            ->orderBy('nom_chefferie')
            ->get()
            ->map(fn($c) => ['code' => $c->code_chefferie, 'name' => $c->nom_chefferie])
            ->toArray();
    }

    #[Computed]
    public function groupements()
    {
        $codeChefferie = $this->showModal ? $this->form->code_chefferie : null;

        if (!$codeChefferie) return [];

        return DB::table('groupements')
            ->select('code_groupement', 'nom_groupement')
            ->where('code_chefferie', $codeChefferie)
            ->orderBy('nom_groupement')
            ->get()
            ->map(fn($g) => ['code' => $g->code_groupement, 'name' => $g->nom_groupement])
            ->toArray();
    }

    #[Computed]
    public function airesantes()
    {
        $codeZone = $this->showModal ? $this->form->code_zonesante : null;

        if (!$codeZone) return [];

        return DB::table('airesantes')
            ->select('code_airesante', 'nom_airesante')
            ->where('code_zonesante', $codeZone)
            ->orderBy('nom_airesante')
            ->get()
            ->map(fn($a) => ['code' => $a->code_airesante, 'name' => $a->nom_airesante])
            ->toArray();
    }

    #[Computed]
    public function evenements()
    {
        return DB::table('evenements')
            ->select('code_evenement', 'nom_evenement')
            ->orderBy('nom_evenement')
            ->get()
            ->map(fn($e) => ['code' => $e->code_evenement, 'name' => $e->nom_evenement])
            ->toArray();
    }

    #[Computed]
    public function superviseursOptions()
    {
        if (!$this->assignIncidentId) return [];
        $incident = Incident::find($this->assignIncidentId);
        if (!$incident) return [];

        return \App\Models\User::query()
            ->where('is_active', true)
            ->where('user_role', 'superviseur')
            ->where('code_province', $incident->code_province)
            ->orderBy('name')
            ->get(['id', 'name', 'email'])
            ->map(fn($u) => ['id' => (string)$u->id, 'name' => $u->name, 'email' => $u->email])
            ->toArray();
    }

    #[Computed]
    public function severityOptions()
    {
        return ['Faible', 'Elevée', 'Critique'];
    }

    #[Computed]
    public function confidentialityOptions()
    {
        return ['Standard', 'Protegé', 'Confidentielle'];
    }

    #[Computed]
    public function survivants()
    {
        return \App\Models\Survivant::query()
            ->select('id', 'code_survivant', 'full_name')
            ->orderByDesc('code_survivant')
            ->limit(200)
            ->get();
    }

    /* -------------------------------------------
     | Reactive hooks
     ------------------------------------------- */
    public function updatedFProvince(): void
    {
        $this->f_territoire = '';
        $this->f_zone = '';
        unset($this->territoires);
        unset($this->zones);
        $this->resetPage();
    }

    public function updatedFTerritoire(): void
    {
        $this->f_zone = '';
        unset($this->zones);
        $this->resetPage();
    }

    public function updatedFormCodeProvince(): void
    {
        $this->form->code_territoire = '';
        $this->form->code_zonesante = '';
        unset($this->territoires);
        unset($this->zones);
    }

    public function updatedFormCodeTerritoire(): void
    {
        $this->form->code_zonesante = '';
        $this->form->code_chefferie = '';
        $this->form->code_groupement = '';
        $this->form->code_airesante = '';
        unset($this->zones);
        unset($this->chefferies);
        unset($this->groupements);
    }

    public function updatedFormCodeChefferie(): void
    {
        $this->form->code_groupement = '';
        unset($this->groupements);
    }

    public function updatedFormCodeZonesante(): void
    {
        $this->form->code_airesante = '';
        unset($this->airesantes);
    }

    public function updatingQ(): void
    {
        $this->resetPage();
    }
    public function updatingFZone(): void
    {
        $this->resetPage();
    }
    public function updatingFStatus(): void
    {
        $this->resetPage();
    }
    public function updatingFSeverite(): void
    {
        $this->resetPage();
    }
    public function updatingFEvenement(): void
    {
        $this->resetPage();
    }
    public function updatingDateFrom(): void
    {
        $this->resetPage();
    }
    public function updatingDateTo(): void
    {
        $this->resetPage();
    }
    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    // incidentRules removed

    /* -------------------------------------------
     | UI Actions (Create/Edit)
     ------------------------------------------- */
    public function openCreate(): void
    {
        $this->resetValidation();
        $this->editing = false;
        $this->editingId = null;
        $this->photo = null;

        $province = $this->isSuperAdmin()
            ? ($this->f_province ?: '')
            : ($this->user()->code_province ?? '');

        $this->form->reset();
        $this->form->date_incident = now()->toDateString();
        $this->form->code_province = $province;

        $this->showModal = true;
    }

    public function openEdit(string $id): void
    {
        $this->resetValidation();
        $this->photo = null;

        $incident = Incident::findOrFail($id);

        if (!$this->canEditIncident($incident)) {
            $this->dispatch('toast', message: "Cet incident ne peut pas être modifié.", type: 'warning', duration: 6000);
            return;
        }

        $this->editing = true;
        $this->editingId = $id;

        $this->form->setIncident($incident);

        $this->showModal = true;
    }

    /* -------------------------------------------
     | Save (Create/Update) -> SERVICE
     ------------------------------------------- */
    public function save(): void
    {
        $this->form->validate();

        $this->validate([
             'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        // Province forcée pour non superadmin
        if (!$this->isSuperAdmin()) {
            $this->form->code_province = $this->user()->code_province;
        }

        // try {
        if ($this->editing && $this->editingId) {
            $incident = Incident::findOrFail($this->editingId);

            if (!$this->canEditIncident($incident)) {
                throw new BusinessRuleException("Modification refusée (incident verrouillé ou rôle).");
            }

            $this->incidentService->update(
                incident: $incident,
                payload: $this->form->all(),
                photo: $this->photo,
                actor: $this->user(),
                ipAddress: request()->ip()
            );

            $this->dispatch('toast', message: "Incident mis à jour.", type: 'success', duration: 5000);
            $this->showModal = false;
            return;
        }

        // Create
        $created = $this->incidentService->create(
            payload: $this->form->all(),
            photo: $this->photo,
            actor: $this->user(),
            ipAddress: request()->ip()
        );

        $this->dispatch('toast', message: "Incident créé : {$created->code_incident}", type: 'success', duration: 5000);
        $this->showModal = false;
        /*  } catch (BusinessRuleException $e) {
            $this->dispatch('toast', message: $e->getMessage(), type: 'warning', duration: 6500);
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('toast', message: "Erreur interne. Veuillez réessayer.", type: 'error', duration: 6500);
        }*/
    }

    /* -------------------------------------------
     | Assign -> SERVICE
     ------------------------------------------- */
    public function openAssign(string $incidentId): void
    {
        $incident = Incident::findOrFail($incidentId);

        if ($incident->statut_incident !== 'En attente') {
            $this->dispatch('toast', message: "Seul un incident en attente peut être assigné.", type: 'warning', duration: 6000);
            return;
        }

        if ($this->isMoniteur()) {
            $this->dispatch('toast', message: "Un moniteur ne peut pas assigner.", type: 'warning', duration: 6000);
            return;
        }

        // Seuls admin/superadmin
        if (!($this->isSuperAdmin() || $this->isAdmin())) {
            $this->dispatch('toast', message: "Seul un admin peut assigner.", type: 'warning', duration: 6000);
            return;
        }

        if (!$this->isSuperAdmin() && !$this->sameProvinceAsUser($incident)) {
            $this->dispatch('toast', message: "Accès refusé.", type: 'error', duration: 6000);
            return;
        }

        $this->assignIncidentId = $incidentId;
        $this->assignTo = $incident->assigned_to ? (string)$incident->assigned_to : null;

        $this->showAssignModal = true;
    }

    public function assign(): void
    {
        if (!$this->assignIncidentId) return;

        $incident = Incident::findOrFail($this->assignIncidentId);

        if ($incident->statut_incident !== 'En attente') {
            $this->dispatch('toast', message: "Seul un incident en attente peut être assigné.", type: 'warning', duration: 6000);
            return;
        }

        if (!($this->isSuperAdmin() || $this->isAdmin())) {
            $this->dispatch('toast', message: "Seul un admin peut assigner.", type: 'warning', duration: 6000);
            return;
        }

        if (!$this->isSuperAdmin() && !$this->sameProvinceAsUser($incident)) {
            $this->dispatch('toast', message: "Accès refusé.", type: 'error', duration: 6000);
            return;
        }

        $this->validate([
            'assignTo' => ['required'],
        ]);

        try {
            $this->incidentService->assignIncident(
                incident: $incident,
                superviseurId: $this->assignTo,
                actor: $this->user(),
                ipAddress: request()->ip()
            );

            $this->showAssignModal = false;
            $this->dispatch('toast', message: "Incident assigné avec succès.", type: 'success', duration: 5000);
        } catch (BusinessRuleException $e) {
            $this->dispatch('toast', message: $e->getMessage(), type: 'warning', duration: 6500);
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('toast', message: "Erreur interne. Veuillez réessayer.", type: 'error', duration: 6500);
        }
    }

    /* -------------------------------------------
     | Confirm modals (Validate/Archive)
     ------------------------------------------- */
    public function askConfirmValidate(string $id): void
    {
        $this->confirmIncidentId = $id;
        $this->confirmTitle = "Valider l'incident ?";
        $this->confirmMessage = "Cette action va passer l'incident au statut « Validé ». Voulez-vous continuer ?";
        $this->confirmAction = 'confirmValidate';
        $this->showConfirmModal = true;
    }

    public function askConfirmArchive(string $id): void
    {
        $this->confirmIncidentId = $id;
        $this->confirmTitle = "Archiver l'incident ?";
        $this->confirmMessage = "L'incident sera archivé et ne sera plus visible dans la liste. Voulez-vous continuer ?";
        $this->confirmAction = 'confirmArchive';
        $this->showConfirmModal = true;
    }

    public function runConfirmAction(): void
    {
        if (!$this->confirmIncidentId) {
            $this->showConfirmModal = false;
            return;
        }

        $id = $this->confirmIncidentId;
        $action = $this->confirmAction;

        $this->showConfirmModal = false;
        $this->confirmIncidentId = null;

        if ($action === 'confirmValidate') {
            $this->validateIncident($id);
            return;
        }

        if ($action === 'confirmArchive') {
            $this->archive($id);
            return;
        }
    }

    /* -------------------------------------------
     | Validate -> SERVICE
     ------------------------------------------- */
    public function validateIncident(string $id): void
    {
        $incident = Incident::findOrFail($id);

        if ($incident->statut_incident !== 'En attente') {
            $this->dispatch('toast', message: "Seul un incident en attente peut être validé.", type: 'warning', duration: 6000);
            return;
        }

        if ($this->isMoniteur()) {
            $this->dispatch('toast', message: "Un moniteur ne peut pas valider.", type: 'warning', duration: 6000);
            return;
        }

        if (!$this->isSuperAdmin() && !$this->sameProvinceAsUser($incident)) {
            $this->dispatch('toast', message: "Accès refusé.", type: 'error', duration: 6000);
            return;
        }

        try {
            $this->incidentService->validateIncident(
                incident: $incident,
                actor: $this->user(),
                ipAddress: request()->ip()
            );

            //Audit log ici

            $this->dispatch('toast', message: "Incident validé.", type: 'success', duration: 5000);
        } catch (BusinessRuleException $e) {
            $this->dispatch('toast', message: $e->getMessage(), type: 'warning', duration: 6500);
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('toast', message: "Erreur interne. Veuillez réessayer.", type: 'error', duration: 6500);
        }
    }

    /* -------------------------------------------
     | Archive -> SERVICE
     ------------------------------------------- */
    public function archive(string $id): void
    {
        $incident = Incident::findOrFail($id);

        if ($this->isMoniteur()) {
            $this->dispatch('toast', message: "Un moniteur ne peut pas archiver.", type: 'warning', duration: 6000);
            return;
        }

        if (!$this->isSuperAdmin() && !$this->sameProvinceAsUser($incident)) {
            $this->dispatch('toast', message: "Accès refusé.", type: 'error', duration: 6000);
            return;
        }

        try {
            $this->incidentService->archiveIncident(
                incident: $incident,
                actor: $this->user(),
                ipAddress: request()->ip()
            );

            $this->dispatch('toast', message: "Incident archivé.", type: 'success', duration: 5000);
        } catch (BusinessRuleException $e) {
            $this->dispatch('toast', message: $e->getMessage(), type: 'warning', duration: 6500);
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('toast', message: "Erreur interne. Veuillez réessayer.", type: 'error', duration: 6500);
        }
    }

    /* -------------------------------------------
     | WhatsApp share (UI only)
     ------------------------------------------- */
    public function shareWhatsapp(string $id): void
    {
        $incident = Incident::query()
            ->leftJoin('provinces', function($join) {
                $join->on('incidents.code_province', '=', 'provinces.code_province')
                     ->where('provinces.is_active', 'YES');
            })
            ->leftJoin('territoires', 'incidents.code_territoire', '=', 'territoires.code_territoire')
            ->leftJoin('zonesantes', 'incidents.code_zonesante', '=', 'zonesantes.code_zonesante')
            ->where('incidents.id', $id)
            ->select([
                'incidents.code_incident',
                'incidents.date_incident',
                'incidents.severite',
                'incidents.statut_incident',
                'incidents.localite',
                'provinces.nom_province',
                'territoires.nom_territoire',
                'zonesantes.nom_zonesante',
            ])->first();

        if (!$incident) return;

        $text =
            "Incident {$incident->code_incident}\n" .
            "Date: " . optional($incident->date_incident)->format('Y-m-d') . "\n" .
            "Province: " . ($incident->nom_province ?? '-') . "\n" .
            "Territoire: " . ($incident->nom_territoire ?? '-') . "\n" .
            "Zone: " . ($incident->nom_zonesante ?? '-') . "\n" .
            "Localité: " . ($incident->localite ?? '-') . "\n" .
            "Sévérité: " . ($incident->severite ?? '-') . "\n" .
            "Statut: " . ($incident->statut_incident ?? '-');

        $url = 'https://wa.me/?text=' . urlencode($text);
        $this->dispatch('open-url', url: $url);
    }

    /* -------------------------------------------
     | Render
     ------------------------------------------- */
    public function render()
    {
        // sleep(5);


        $user = $this->user();

        $query = Incident::query()
            ->where('incidents.statut_incident', '!=', 'Archivé')
            ->leftJoin('provinces', function($join) {
                $join->on('incidents.code_province', '=', 'provinces.code_province')
                     ->where('provinces.is_active', 'YES');
            })
            ->leftJoin('zonesantes', 'incidents.code_zonesante', '=', 'zonesantes.code_zonesante')
            ->leftJoin('evenements', 'incidents.code_evenement', '=', 'evenements.code_evenement')
            ->with(['violences:id,violence_name'])
            ->select([
                'incidents.*',
                DB::raw("COALESCE(provinces.nom_province, incidents.code_province, 'N/A') as province_name"),
                DB::raw("COALESCE(zonesantes.nom_zonesante, incidents.code_zonesante, 'N/A') as zone_name"),
                DB::raw("COALESCE(evenements.nom_evenement, incidents.code_evenement, 'N/A') as evenement_name"),
            ]);

        // Scope province
        if (!$this->isSuperAdmin()) {
            $query->where('incidents.code_province', $user->code_province);
        } else {
            if ($this->f_province !== '') {
                $query->where('incidents.code_province', $this->f_province);
            }
        }

        // Filtres
        if ($this->q !== '') {
            $s = '%' . trim($this->q) . '%';
            $query->where('incidents.code_incident', 'ilike', $s);
        }

        if ($this->f_status !== '') $query->where('incidents.statut_incident', $this->f_status);
        if ($this->f_severite !== '') $query->where('incidents.severite', $this->f_severite);

        if ($this->f_territoire !== '') $query->where('incidents.code_territoire', $this->f_territoire);
        if ($this->f_zone !== '') $query->where('incidents.code_zonesante', $this->f_zone);
        if ($this->f_evenement !== '') $query->where('incidents.code_evenement', $this->f_evenement);

        if ($this->date_from) $query->whereDate('incidents.date_incident', '>=', $this->date_from);
        if ($this->date_to) $query->whereDate('incidents.date_incident', '<=', $this->date_to);

        $incidents = $query
            ->orderByDesc('incidents.date_incident')
            ->paginate($this->perPage);

        return view('livewire.pages.incidents.index', [
            'incidents' => $incidents,
            'statuses' => ['En attente', 'Validé', 'Cloturée', 'Archivé'],
        ]);
    }
}
