<?php

namespace App\Livewire\Components;

use App\Models\AuditLog;
use App\Models\Incident;
use App\Models\Referencement;
use App\Models\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Services\ReferencementService;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\CaseNote;

class IncidentReferencements extends Component
{
    use WithFileUploads;

    public string $incidentId;

    public bool $showModal = false;
    public bool $editing = false;
    public ?string $editingId = null; // uuid referencement
    public ?string $incidentStatus = null;

    public bool $showQuickProviderModal = false;

    public $file; // TemporaryUploadedFile|null

    protected $listeners = [
        'incidentStatusChanged' => 'refreshIncidentStatus',
    ];

    public array $form = [
        'date_referencement' => null,
        'provider_id' => null,
        'resultat' => null,
        'type_reponse' => null,
        'statut_reponse' => 'En attente',
        'besoin_suivi' => false,
        'observations' => null,
        'file_path' => null,
    ];

    public array $providerForm = [
        'provider_name' => '',
        'provider_location' => '',
        'focalpoint_name' => '',
        'focalpoint_email' => '',
        'focalpoint_number' => '',
        'type_services_proposes' => [], // array multi-select
    ];

    public function mount(string $incidentId): void
    {
        $this->incidentId = $incidentId;

        $this->incidentStatus = \App\Models\Incident::query()
            ->where('id', $this->incidentId)
            ->value('statut_incident');
    }

    public function refreshIncidentStatus(): void
    {
        $this->incidentStatus = \App\Models\Incident::query()
            ->where('id', $this->incidentId)
            ->value('statut_incident');
    }

    /* ------------------------- Guards & Helpers ------------------------- */

    #[Computed]
    public function typeOptions(): array
    {
        return [
            'Prise en charge médicale',
            'Soutien psychologique et psychosocial',
            'Prise en charge juridique et judiciaire',
            'Soutien socio-économique',
            'Gestion de cas',
            'Autre',
        ];
    }

    #[Computed]
    public function statusOptions(): array
    {
        return ['En attente', 'En cours', 'Fournie', 'refusée'];
    }

    private function userRole(): string
    {
        return Auth::user()->user_role ?? '';
    }

    private function isSuperadmin(): bool
    {
        return $this->userRole() === 'superadmin';
    }

    private function isMoniteur(): bool
    {
        return $this->userRole() === 'moniteur';
    }

    private function canWrite(): bool
    {
        return in_array($this->userRole(), ['superadmin', 'admin', 'superviseur'], true);
    }

    private function incident(): Incident
    {
        return Incident::query()->findOrFail($this->incidentId);
    }

    private function isLocked(Incident $incident): bool
    {
        return in_array($incident->statut_incident, ['Cloturée', 'Archivé'], true);
    }

    private function sameProvinceAsUser(Incident $incident): bool
    {
        $u = Auth::user();
        if (($u->user_role ?? '') === 'superadmin') return true;
        return ($u->code_province ?? null) && ($u->code_province === $incident->code_province);
    }

    private function rules(): array
    {
        return [
            'form.date_referencement' => ['required', 'date', 'before_or_equal:today'],
            'form.type_reponse' => ['required', Rule::in($this->typeOptions)],
            'form.provider_id' => ['required', 'integer', Rule::exists('service_providers', 'id')],
            'form.statut_reponse' => ['required', Rule::in($this->statusOptions)],
            'form.besoin_suivi' => ['boolean'],
            'form.resultat' => ['nullable', 'string'],
            'form.observations' => ['nullable', 'string'],
            'file' => ['nullable', 'file', 'max:4096'],
        ];
    }

    private function ensureIncidentIsReferencableOrToast(Incident $incident): bool
    {
        if ($this->isMoniteur()) {
            $this->dispatch('toast', message: "Un moniteur ne peut pas faire de référencement.", type: 'warning', duration: 6000);
            return false;
        }

        if (!$this->canWrite()) {
            $this->dispatch('toast', message: "Action non autorisée.", type: 'error', duration: 6000);
            return false;
        }

        if (!$this->sameProvinceAsUser($incident)) {
            $this->dispatch('toast', message: "Accès refusé (province).", type: 'error', duration: 6000);
            return false;
        }

        if ($this->isLocked($incident)) {
            $this->dispatch('toast', message: "Incident clôturé/archivé : référencement impossible.", type: 'warning', duration: 6000);
            return false;
        }

        if (($incident->statut_incident ?? '') !== 'Validé') {
            $this->dispatch('toast', message: "L’incident doit être validé avant tout référencement.", type: 'warning', duration: 6500);
            return false;
        }

        return true;
    }

    /* ------------------------------ Providers (filtered) ------------------------------ */

    public function getProvidersFilteredProperty()
    {
        $type = $this->form['type_reponse'] ?? null;
        if (!$type) {
            return collect();
        }

        // type_services_proposes est stocké en TEXT (JSON string)
        // On filtre côté PHP pour éviter du JSON SQL compliqué.
        return ServiceProvider::query()
            ->orderBy('provider_name')
            ->get()
            ->filter(function ($p) use ($type) {
                $raw = $p->type_services_proposes;
                $arr = is_array($raw) ? $raw : (json_decode((string)$raw, true) ?: []);
                return in_array($type, $arr, true);
            })
            ->values();
    }

    public function getSelectedProviderProperty(): ?ServiceProvider
    {
        $id = $this->form['provider_id'] ?? null;
        if (!$id) return null;
        return ServiceProvider::find($id);
    }

    public function updatedFormTypeReponse(): void
    {
        // reset provider when type changes
        $this->form['provider_id'] = null;
    }

    /* ------------------------------ UI ------------------------------ */

    public function openCreate(): void
    {
        $incident = $this->incident();
        if (!$this->ensureIncidentIsReferencableOrToast($incident)) return;

        $this->resetValidation();
        $this->editing = false;
        $this->editingId = null;
        $this->file = null;

        $this->form = [
            'date_referencement' => now()->toDateString(),
            'provider_id' => null,
            'resultat' => null,
            'type_reponse' => null,
            'statut_reponse' => 'En attente',
            'besoin_suivi' => false,
            'observations' => null,
            'file_path' => null,
        ];

        $this->showModal = true;
    }

    public function openEdit(string $id): void
    {
        $incident = $this->incident();
        if (!$this->ensureIncidentIsReferencableOrToast($incident)) return;

        $ref = Referencement::query()
            ->where('id', $id)
            ->where('id_incident', $this->incidentId)
            ->firstOrFail();

        $this->resetValidation();
        $this->editing = true;
        $this->editingId = $ref->id;
        $this->file = null;

        $this->form = [
            'date_referencement' => optional($ref->date_referencement)->format('Y-m-d'),
            'provider_id' => $ref->provider_id,
            'resultat' => $ref->resultat,
            'type_reponse' => $ref->type_reponse,
            'statut_reponse' => $ref->statut_reponse ?? 'En attente',
            'besoin_suivi' => (bool)$ref->besoin_suivi,
            'observations' => $ref->observations,
            'file_path' => $ref->file_path,
        ];

        $this->showModal = true;
    }

    public function save(): void
    {
        $incident = $this->incident();
        if (!$this->ensureIncidentIsReferencableOrToast($incident)) return;

        $this->validate($this->rules());

        $service = app(ReferencementService::class);

        if ($this->editing && $this->editingId) {
            $service->update(
                $this->editingId,
                $this->form,
                $this->file,
                Auth::user(),
                request()->ip()
            );
        } else {
            $service->create(
                $this->incidentId,
                $this->form,
                $this->file,
                Auth::user(),
                request()->ip()
            );
        }

        $this->dispatch('toast', message: $this->editing ? "Référencement mis à jour." : "Référencement enregistré.", type: 'success', duration: 5000);
        $this->showModal = false;
        $this->editing = false;
        $this->editingId = null;
        $this->file = null;

        $this->dispatch('referencements-updated');
    }

    /* ------------------------- Quick Create Provider ------------------------- */

    public function openQuickProviderCreate(): void
    {
        if (!$this->isSuperadmin()) {
            $this->dispatch('toast', message: "Seuls les superadmins peuvent créer une structure.", type: 'warning', duration: 6000);
            return;
        }

        $type = $this->form['type_reponse'] ?? null;

        $this->resetValidation();

        $this->providerForm = [
            'provider_name' => '',
            'provider_location' => '',
            'focalpoint_name' => '',
            'focalpoint_email' => '',
            'focalpoint_number' => '',
            'type_services_proposes' => $type ? [$type] : [],
        ];

        $this->showQuickProviderModal = true;
    }

    public function saveQuickProvider(): void
    {
        if (!$this->isSuperadmin()) {
            $this->dispatch('toast', message: "Action non autorisée.", type: 'error', duration: 6000);
            return;
        }

        $this->validate([
            'providerForm.provider_name' => ['required', 'string', 'max:255', Rule::unique('service_providers', 'provider_name')],
            'providerForm.provider_location' => ['nullable', 'string', 'max:255'],
            'providerForm.focalpoint_name' => ['nullable', 'string', 'max:255'],
            'providerForm.focalpoint_email' => ['nullable', 'email', 'max:255'],
            'providerForm.focalpoint_number' => ['nullable', 'string', 'max:50'],
            'providerForm.type_services_proposes' => ['array', 'min:1'],
        ]);

        $p = new ServiceProvider();
        $p->provider_name = $this->providerForm['provider_name'];
        $p->provider_location = $this->providerForm['provider_location'] ?: null;
        $p->focalpoint_name = $this->providerForm['focalpoint_name'] ?: null;
        $p->focalpoint_email = $this->providerForm['focalpoint_email'] ?: null;
        $p->focalpoint_number = $this->providerForm['focalpoint_number'] ?: null;
        $p->type_services_proposes = json_encode($this->providerForm['type_services_proposes'], JSON_UNESCAPED_UNICODE);
        $p->created_by = Auth::id();
        $p->save();

        AuditLog::create([
            'id' => random_int(100000000, 999999999),
            'user_id' => Auth::id(),
            'user_action' => 'provider_created',
            'model_type' => 'service_provider',
            'model_id' => null,
            'ip_address' => request()->ip(),
            'action_meta' => json_encode([
                'provider_id' => $p->id,
                'provider_name' => $p->provider_name,
                'services' => $this->providerForm['type_services_proposes'],
            ], JSON_UNESCAPED_UNICODE),
        ]);

        // sélection automatique dans le formulaire
        $this->form['provider_id'] = $p->id;

        $this->showQuickProviderModal = false;

        $this->dispatch('toast', message: "Structure créée et sélectionnée.", type: 'success', duration: 5000);
    }

    public function getReferencementsProperty()
    {
        return \App\Models\Referencement::query()
            ->with(['provider:id,provider_name,provider_location,focalpoint_number', 'author:id,name'])
            ->where('id_incident', $this->incidentId)
            ->orderByDesc('date_referencement')
            ->get();
    }



    public function render()
    {
        $incident = $this->incident();

        $referencements = Referencement::query()
            ->where('id_incident', $this->incidentId)
            ->with(['provider:id,provider_name,provider_location,focalpoint_number', 'author:id,name'])
            ->orderBy('date_referencement', 'desc')
            ->get();

        return view('livewire.components.incident-referencements', [
            'referencements' => $referencements,
            'incidentStatus' => $incident->statut_incident,
            'canWrite' => $this->canWrite() && !$this->isLocked($incident) && $this->sameProvinceAsUser($incident),
        ]);
    }
}
