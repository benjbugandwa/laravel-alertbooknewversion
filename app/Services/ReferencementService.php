<?php

namespace App\Services;

use App\Exceptions\BusinessRuleException;
use App\Models\AuditLog;
use App\Models\Incident;
use App\Models\Referencement;
use App\Models\ServiceProvider;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ReferencementService
{
    private function ensureIncidentValidated(Incident $incident): void
    {
        if (($incident->statut_incident ?? '') !== 'Validé') {
            throw new BusinessRuleException("Référencement impossible : l'incident doit être validé.");
        }
        if (in_array($incident->statut_incident, ['Cloturée', 'Archivé'], true)) {
            throw new BusinessRuleException("Référencement impossible : incident clôturé ou archivé.");
        }
    }

    private function ensureNotMoniteur(User $actor): void
    {
        if ($actor->user_role === 'moniteur') {
            throw new BusinessRuleException("Un moniteur ne peut pas enregistrer de référencement.");
        }
    }

    private function nextCode(): string
    {
        // Version robuste (pas de CAST) : on trie sur code fixe REF-000000
        $last = DB::table('referencements')
            ->where('code_referencement', 'like', 'REF-%')
            ->orderByDesc('code_referencement')
            ->value('code_referencement');

        $n = 0;
        if (is_string($last) && preg_match('/^REF-(\d{6})$/', $last, $m)) {
            $n = (int) $m[1];
        }

        return 'REF-' . str_pad((string)($n + 1), 6, '0', STR_PAD_LEFT);
    }

    private function audit(User $actor, string $action, string $modelType, string $modelId, string $ipAddress, array $meta = []): void
    {
        AuditLog::create([
            'id' => random_int(100000000, 999999999),
            'user_id' => $actor->id,
            'user_action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId, // uuid ok
            'ip_address' => $ipAddress,
            'action_meta' => json_encode($meta, JSON_UNESCAPED_UNICODE),
        ]);
    }

    public function create(
        string $incidentId,
        array $payload,
        ?UploadedFile $file,
        User $actor,
        string $ipAddress
    ): Referencement {
        $this->ensureNotMoniteur($actor);

        return DB::transaction(function () use ($incidentId, $payload, $file, $actor, $ipAddress) {

            $incident = Incident::findOrFail($incidentId);
            $this->ensureIncidentValidated($incident);

            // sécurité: provider doit exister
            $provider = ServiceProvider::findOrFail((int)($payload['provider_id'] ?? 0));

            $ref = new Referencement();
            $ref->code_referencement = $this->nextCode();
            $ref->id_incident = $incident->id;

            $ref->date_referencement = $payload['date_referencement'] ?? now();
            $ref->provider_id = $provider->id;

            $ref->resultat = $payload['resultat'] ?? null;
            $ref->type_reponse = $payload['type_reponse'] ?? null;
            $ref->statut_reponse = $payload['statut_reponse'] ?? 'En attente';

            $ref->besoin_suivi = (bool)($payload['besoin_suivi'] ?? false);
            $ref->observations = $payload['observations'] ?? null;

            $ref->created_by = $actor->id;

            if ($file) {
                $path = $file->store('referencements', 'public');
                $ref->file_path = $path;
            }

            $ref->save();

            $this->audit($actor, 'referencement_created', 'referencement', (string)$ref->id, $ipAddress, [
                'incident_id' => $incident->id,
                'provider_id' => $provider->id,
                'type_reponse' => $ref->type_reponse,
                'statut_reponse' => $ref->statut_reponse,
            ]);

            return $ref;
        });
    }

    public function update(
        string $refId,
        array $payload,
        ?UploadedFile $file,
        User $actor,
        string $ipAddress
    ): Referencement {
        $this->ensureNotMoniteur($actor);

        return DB::transaction(function () use ($refId, $payload, $file, $actor, $ipAddress) {

            $ref = Referencement::findOrFail($refId);
            $incident = Incident::findOrFail($ref->id_incident);

            $this->ensureIncidentValidated($incident);

            if (!empty($payload['provider_id'])) {
                $provider = ServiceProvider::findOrFail((int)$payload['provider_id']);
                $ref->provider_id = $provider->id;
            }

            if (!empty($payload['date_referencement'])) {
                $ref->date_referencement = $payload['date_referencement'];
            }

            $ref->resultat = $payload['resultat'] ?? $ref->resultat;
            $ref->type_reponse = $payload['type_reponse'] ?? $ref->type_reponse;
            $ref->statut_reponse = $payload['statut_reponse'] ?? $ref->statut_reponse;
            $ref->besoin_suivi = (bool)($payload['besoin_suivi'] ?? $ref->besoin_suivi);
            $ref->observations = $payload['observations'] ?? $ref->observations;

            if ($file) {
                if ($ref->file_path) {
                    Storage::disk('public')->delete($ref->file_path);
                }
                $path = $file->store('referencements', 'public');
                $ref->file_path = $path;
            }

            $ref->save();

            $this->audit($actor, 'referencement_updated', 'referencement', (string)$ref->id, $ipAddress, [
                'incident_id' => $incident->id,
                'provider_id' => $ref->provider_id,
                'type_reponse' => $ref->type_reponse,
                'statut_reponse' => $ref->statut_reponse,
            ]);

            return $ref;
        });
    }
}
