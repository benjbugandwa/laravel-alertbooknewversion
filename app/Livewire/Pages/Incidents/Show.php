<?php

namespace App\Livewire\Pages\Incidents;

use App\Models\Incident;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditLog;

class Show extends Component
{
    public Incident $incident;

    // Confirmation modal
    public bool $showConfirmModal = false;
    public string $confirmTitle = '';
    public string $confirmMessage = '';
    public string $confirmAction = ''; // 'validate'

    protected $listeners = ['violences-updated' => 'refreshIncident'];

    public function refreshIncident(): void
    {
        $this->incident->refresh()->load(['violences:id,violence_name,categorie_name']);
    }

    public function mount(Incident $incident): void
    {
        // Sécurité province (superadmin voit tout)
        $user = Auth::user();

        if ($user->user_role !== 'superadmin' && $incident->code_province !== $user->code_province) {
            abort(403);
        }

        // Les incidents archivés ne sont pas visibles
        if ($incident->statut_incident === 'Archivé') {
            abort(404);
        }

        $this->incident = $incident->load([
            'violences:id,violence_name,categorie_name',
        ]);

        // $this->incident = $incident;
    }

    private function canValidate(): bool
    {
        $role = Auth::user()->user_role;

        if (!in_array($role, ['superadmin', 'admin', 'superviseur'], true)) {
            return false;
        }

        // Empêcher si verrouillé ou déjà validé
        if (in_array($this->incident->statut_incident, ['Cloturée', 'Archivé'], true)) {
            return false;
        }

        if ($this->incident->statut_incident === 'Validé') {
            return false;
        }

        return true;
    }

    public function askConfirmValidate(): void
    {
        if (!$this->canValidate()) {
            $this->dispatch('toast', message: "Vous n'êtes pas autorisé à valider cet incident.", type: 'warning', duration: 6000);
            return;
        }

        $this->confirmTitle = "Valider l’incident ?";
        $this->confirmMessage = "Cette action changera le statut de l’incident en « Validé ». Voulez-vous continuer ?";
        $this->confirmAction = 'validate';
        $this->showConfirmModal = true;
    }

    public function runConfirmAction(): void
    {
        $this->showConfirmModal = false;

        if ($this->confirmAction === 'validate') {
            $this->validateIncident();
            return;
        }

        if ($this->confirmAction === 'archive') {
            $this->archiveIncident();
            return;
        }
    }

    public function archiveIncident(): void
    {
        if (!$this->canArchive()) {
            $this->dispatch('toast', message: "Action non autorisée.", type: 'warning', duration: 6000);
            return;
        }

        $oldStatus = $this->incident->statut_incident;

        $this->incident->statut_incident = 'Archivé';
        $this->incident->last_status_changed_at = now();
        $this->incident->save();

        // Audit log
        \App\Models\AuditLog::create([
            'id' => random_int(100000000, 999999999),
            'user_id' => Auth::user()->id,
            'user_action' => 'incident_archived',
            'model_type' => 'incident',
            'model_id' => $this->incident->id, // UUID
            'ip_address' => request()->ip(),
            'action_meta' => json_encode([
                'code_incident' => $this->incident->code_incident,
                'old_status' => $oldStatus,
                'new_status' => 'Archivé',
            ], JSON_UNESCAPED_UNICODE),
        ]);

        // Comme archivé n'est plus visible, on redirige vers la liste
        session()->flash('success', "Incident archivé.");
        $this->redirect(route('incidents.index'), navigate: true);
    }

    private function canArchive(): bool
    {
        $role = Auth::user()->user_role;

        // seuls admin/superviseur/superadmin
        if (!in_array($role, ['superadmin', 'admin', 'superviseur'], true)) {
            return false;
        }

        // si déjà archivé/clôturé => bloqué
        if (in_array($this->incident->statut_incident, ['Cloturée', 'Archivé'], true)) {
            return false;
        }

        return true;
    }

    public function askConfirmArchive(): void
    {
        if (!$this->canArchive()) {
            $this->dispatch('toast', message: "Vous n'êtes pas autorisé à archiver cet incident.", type: 'warning', duration: 6000);
            return;
        }

        $this->confirmTitle = "Archiver l’incident ?";
        $this->confirmMessage = "L’incident sera archivé et ne sera plus visible dans la liste. Voulez-vous continuer ?";
        $this->confirmAction = 'archive';
        $this->showConfirmModal = true;
    }




    public function validateIncident(): void
    {
        if (!$this->canValidate()) {
            $this->dispatch('toast', message: "Action non autorisée.", type: 'warning', duration: 6000);
            return;
        }

        $this->incident->statut_incident = 'Validé';
        $this->incident->last_status_changed_at = now();
        $this->incident->save();

        // Audit log
        AuditLog::create([
            //'id' => random_int(100000000, 999999999),
            'user_id' => Auth::user()->id,
            'user_action' => 'incident_validated',
            'model_type' => 'incident',
            'model_id' => $this->incident->id, // UUID
            'ip_address' => request()->ip(),
            'action_meta' => json_encode([
                'code_incident' => $this->incident->code_incident,
                'old_status' => 'En attente',
                'new_status' => 'Validé',
            ], JSON_UNESCAPED_UNICODE),
        ]);

        // Refresh modèle pour l’UI
        $this->incident->refresh()->load(['violences:id,violence_name,categorie_name']);

        $this->dispatch('toast', message: "Incident validé avec succès.", type: 'success', duration: 5000);
    }

    public function render()
    {
        // sleep(5);
        return view('livewire.pages.incidents.show');
    }
}
