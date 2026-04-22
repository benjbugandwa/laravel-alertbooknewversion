<?php

namespace App\Livewire\Components;

use App\Models\AuditLog;
use App\Models\CaseNote;
use App\Models\Incident;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Livewire\Forms\CaseNoteForm;
use App\Services\CaseNotesService;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;

class IncidentCaseNotes extends Component
{
    use WithFileUploads;

    public string $incidentId;

    // public bool $showEditModal = false;
    public bool $editing = false;
    public ?int $editingId = null;
    public bool $showEditModal = false;

    public CaseNoteForm $form;
    public CaseNoteForm $editForm;

    public $file; // TemporaryUploadedFile|null

    public function mount(string $incidentId): void
    {
        $this->incidentId = $incidentId;
    }

    /* ------------------------- Guards & Helpers ------------------------- */

    private function userRole(): string
    {
        return Auth::user()->user_role ?? '';
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


    // Removed local rules

    /* ------------------------------ UI ------------------------------ */

    public function openCreate(): void
    {
        $incident = $this->incident();

        if (!$this->canWrite()) {
            $this->dispatch('toast', "Vous n'avez pas l'autorisation d'ajouter une note.", 'error', 6000);
            return;
        }
        if (!$this->sameProvinceAsUser($incident)) {
            $this->dispatch('toast', "Accès refusé (province).", 'error', 6000);
            return;
        }
        if ($this->isLocked($incident)) {
            $this->dispatch('toast', "Incident clôturé/archivé : ajout de note impossible.", 'warning', 6000);
            return;
        }

        $this->resetValidation();
        $this->editing = false;
        $this->editingId = null;
        $this->file = null;

        $this->form->reset();

        $this->showEditModal = true;
    }

    public function openEdit(int $id): void
    {
        $incident = $this->incident();

        if (!$this->canWrite()) {
            $this->dispatch('toast', "Vous n'avez pas l'autorisation de modifier une note.", 'error', 6000);
            return;
        }
        if (!$this->sameProvinceAsUser($incident)) {
            $this->dispatch('toast', "Accès refusé (province).", 'error', 6000);
            return;
        }
        if ($this->isLocked($incident)) {
            $this->dispatch('toast', "Incident clôturé/archivé : modification impossible.", 'warning', 6000);
            return;
        }

        $note = CaseNote::query()
            ->where('id', $id)
            ->where('id_incident', $this->incidentId)
            ->firstOrFail();

        $this->resetValidation();
        $this->editing = true;
        $this->editingId = $note->id;
        $this->file = null;

        $this->editForm->setCaseNote($note);

        $this->showEditModal = true;
    }

    public function closeModalAndReset(): void
    {
        $this->showEditModal = false;
        $this->editing = false;
        $this->editingId = null;

        $this->form->reset();
        $this->editForm->reset();
    }

    public function save(): void
    {
        $incident = $this->incident();

        if (!$this->canWrite()) {
            $this->dispatch('toast', "Action non autorisée.", 'error', 6000);
            return;
        }
        if (!$this->sameProvinceAsUser($incident)) {
            $this->dispatch('toast', "Accès refusé (province).", 'error', 6000);
            return;
        }
        if ($this->isLocked($incident)) {
            $this->dispatch('toast', "Incident clôturé/archivé : opération impossible.", 'warning', 6000);
            return;
        }

        $this->form->validate();

        $this->validate([
            'file' => ['nullable', 'file', 'max:4096'],
        ]);

        $service = app(CaseNotesService::class);
        $payload = [
            'case_note' => $this->form->case_note,
            'is_confidential' => $this->form->is_confidential,
        ];

        if ($this->editing && $this->editingId) {
            $service->updateNote(
                (string)$this->editingId,
                ['case_note' => $this->editForm->case_note, 'is_confidential' => $this->editForm->is_confidential],
                $this->file,
                Auth::user(),
                request()->ip()
            );
        } else {
            $service->createNote(
                $this->incidentId,
                $payload,
                $this->file,
                Auth::user(),
                request()->ip()
            );
        }

        $this->dispatch('toast', $this->editing ? "Note mise à jour." : "Note ajoutée.", 'success', 5000);
        $this->showEditModal = false;
        $this->editing = false;
        $this->editingId = null;
        
        // Very important: reset the file upload explicitly to clear the DOM input.
        $this->file = null;
        $this->form->reset();
        $this->reset('file');

        // Refresh computing properties
        unset($this->notes);

        $this->dispatch('case-notes-updated');
    }

    public function closeEditModal(): void
    {
        $this->showEditModal = false;
        $this->editingId = null;

        $this->editForm->reset();
    }

    public function update(): void
    {
        $incident = $this->incident();
        $this->editForm->validate();

        $this->validate([
            'file' => ['nullable', 'file', 'max:4096'],
        ]);

        $service = app(CaseNotesService::class);
        $payload = [
            'case_note' => $this->editForm->case_note,
            'is_confidential' => $this->editForm->is_confidential,
        ];

        $service->updateNote(
            (string)$this->editingId,
            $payload,
            $this->file,
            Auth::user(),
            request()->ip()
        );

        $this->dispatch('toast', 'Note modifiée.', 'success', 5000);

        unset($this->notes);
        
        $this->showEditModal = false;
        $this->editing = false;
        $this->editingId = null;
        $this->file = null;

        $this->form->reset();

        $this->closeEditModal();
    }

    #[Computed]
    public function notes()
    {
        return CaseNote::query()
            ->where('id_incident', $this->incidentId)
            ->leftJoin('users', 'case_notes.created_by', '=', 'users.id')
            ->orderBy('case_notes.created_at', 'asc')
            ->get([
                'case_notes.*',
                DB::raw("COALESCE(users.name, '—') as author_name"),
            ]);
    }

    public function render()
    {
        $incident = $this->incident();

        return view('livewire.components.incident-case-notes', [
            'incidentStatus' => $incident->statut_incident,
            'canWrite' => $this->canWrite() && !$this->isLocked($incident) && $this->sameProvinceAsUser($incident),
        ]);
    }
}
