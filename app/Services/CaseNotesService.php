<?php

namespace App\Services;

use App\Exceptions\BusinessRuleException;
use App\Models\AuditLog;
use App\Models\CaseNote;
use App\Models\Incident;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CaseNotesService
{
    /**
     * Règle: incident non "Cloturée" et non "Archivé".
     */
    private function ensureIncidentNotLocked(Incident $incident): void
    {
        if (in_array($incident->statut_incident, ['Cloturée', 'Archivé'], true)) {
            throw new BusinessRuleException("Action impossible : incident clôturé ou archivé.");
        }
    }

    private function audit(User $actor, string $action, string $modelType, string $modelId, string $ipAddress, array $meta = []): void
    {
        AuditLog::create([
            //'id' => random_int(100000000, 999999999),
            'user_id' => $actor->id,
            'user_action' => $action, // <-- on corrige juste après (typo)
            'model_type' => $modelType,
            'model_id' => $modelId,
            'ip_address' => $ipAddress,
            'action_meta' => json_encode($meta, JSON_UNESCAPED_UNICODE),
        ]);
    }

    /**
     * Ajoute une note à un incident.
     */
    public function createNote(
        string $incidentId,
        array $payload,
        ?UploadedFile $file,
        User $actor,
        string $ipAddress
    ): CaseNote {
        return DB::transaction(function () use ($incidentId, $payload, $file, $actor, $ipAddress) {

            $incident = Incident::findOrFail($incidentId);
            $this->ensureIncidentNotLocked($incident);

            $note = new CaseNote();
            $note->id_incident = $incident->id;
            $note->case_note = $payload['case_note'] ?? '';
            $note->is_confidential = (bool)($payload['is_confidential'] ?? false);
            $note->created_by = $actor->id;

            if ($file) {
                $path = $file->store('case_notes', 'public');
                $note->file_path = $path;
            }

            $note->save();

            $this->audit($actor, 'case_note_created', 'case_note', (string) $note->id, $ipAddress, [
                'incident_id' => $incident->id,
                'is_confidential' => $note->is_confidential,
                'has_file' => (bool)$note->file_path,
            ]);

            return $note;
        });
    }

    /**
     * Modifie une note (seulement si incident non verrouillé).
     */
    public function updateNote(
        string $noteId,
        array $payload,
        ?UploadedFile $file,
        User $actor,
        string $ipAddress
    ): CaseNote {
        return DB::transaction(function () use ($noteId, $payload, $file, $actor, $ipAddress) {

            $note = CaseNote::findOrFail($noteId);
            $incident = Incident::findOrFail($note->id_incident);

            $this->ensureIncidentNotLocked($incident);

            $note->case_note = $payload['case_note'] ?? $note->case_note;
            $note->is_confidential = (bool)($payload['is_confidential'] ?? $note->is_confidential);

            // si nouveau fichier, on remplace (optionnel)
            if ($file) {
                if ($note->file_path) {
                    Storage::disk('public')->delete($note->file_path);
                }
                $path = $file->store('case_notes', 'public');
                $note->file_path = $path;
            }

            $note->save();

            $this->audit($actor, 'case_note_updated', 'case_note', (string) $note->id, $ipAddress, [
                'incident_id' => $incident->id,
                'is_confidential' => $note->is_confidential,
                'has_file' => (bool)$note->file_path,
            ]);

            return $note;
        });
    }
}
