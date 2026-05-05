<?php

namespace App\Services;

use App\Exceptions\BusinessRuleException;
use App\Mail\IncidentAssignedMail;
use App\Mail\IncidentNeedsValidationMail;
use App\Mail\NewIncidentNotificationMail;

use App\Models\AuditLog;
use App\Models\Incident;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class IncidentService
{
    /**
     * Crée un incident + audit + notification superviseurs.
     */
    public function create(array $payload, ?UploadedFile $photo, User $actor, string $ipAddress): Incident
    {
        return DB::transaction(function () use ($payload, $photo, $actor, $ipAddress) {

            // Province forcée si pas superadmin
            if ($actor->user_role !== 'superadmin') {
                $payload['code_province'] = $actor->code_province;
            }

            $incident = new Incident();
            $incident->fill($payload);

            $incident->code_incident = $this->nextIncidentCode();
            $incident->created_by = $actor->id;
            $incident->last_status_changed_at = now();

            if ($photo) {
                $incident->photo_url = $photo->store('incidents', 'public');
            }

            $incident->save();

            $this->audit(
                action: 'incident_created',
                modelType: 'incident',
                modelId: (string) $incident->id,
                ipAddress: $ipAddress,
                actor: $actor,
                meta: [
                    'code_incident' => $incident->code_incident,
                    'province' => $incident->code_province,
                    'statut' => $incident->statut_incident,
                    'severite' => $incident->severite,
                ]
            );

            $this->notifySupervisorsOfNewIncident($incident);

            return $incident;

        });
    }

    /**
     * Met à jour un incident + audit.
     */
    public function update(Incident $incident, array $payload, ?UploadedFile $photo, User $actor, string $ipAddress): Incident
    {
        return DB::transaction(function () use ($incident, $payload, $photo, $actor, $ipAddress) {

            if ($this->isLocked($incident)) {
                throw new BusinessRuleException("Incident clôturé/archivé : modification impossible.");
            }

            // Moniteur ne peut pas modifier
            if ($actor->user_role === 'moniteur') {
                throw new BusinessRuleException("Un moniteur ne peut pas modifier un incident.");
            }

            // Scope province
            if ($actor->user_role !== 'superadmin' && $actor->code_province !== $incident->code_province) {
                throw new BusinessRuleException("Accès refusé.");
            }

            // Province forcée si pas superadmin
            if ($actor->user_role !== 'superadmin') {
                $payload['code_province'] = $actor->code_province;
            }

            $incident->fill($payload);
            $incident->last_status_changed_at = now();

            if ($photo) {
                $incident->photo_url = $photo->store('incidents', 'public');
            }

            $incident->save();

            $this->audit(
                action: 'incident_updated',
                modelType: 'incident',
                modelId: (string) $incident->id,
                ipAddress: $ipAddress,
                actor: $actor,
                meta: [
                    'statut' => $incident->statut_incident,
                    'severite' => $incident->severite,
                ]
            );

            return $incident;
        });
    }

    /**
     * Assigne un incident à un superviseur + audit + mail.
     * IMPORTANT: paramètres nommés attendus: superviseurId, payload (comme tu utilises).
     */
    public function assignIncident(
        Incident $incident,
        string $superviseurId,
        User $actor,
        string $ipAddress,
        array $payload = []
    ): Incident {
        return DB::transaction(function () use ($incident, $superviseurId, $actor, $ipAddress, $payload) {

            if ($incident->statut_incident !== 'En attente') {
                throw new BusinessRuleException("Seul un incident en attente peut être assigné.");
            }

            // Seuls admin/superadmin
            if (!in_array($actor->user_role, ['superadmin', 'admin'], true)) {
                throw new BusinessRuleException("Seul un admin peut assigner.");
            }

            // Scope province
            if ($actor->user_role !== 'superadmin' && $actor->code_province !== $incident->code_province) {
                throw new BusinessRuleException("Accès refusé.");
            }

            /** @var User|null $superviseur */
            $superviseur = User::query()
                ->where('id', $superviseurId)
                ->where('is_active', true)
                ->where('user_role', 'superviseur')
                ->where('code_province', $incident->code_province)
                ->first();

            if (!$superviseur) {
                throw new BusinessRuleException("Superviseur invalide (inactif / mauvais rôle / mauvaise province).");
            }

            $incident->assigned_to = $superviseur->id; // tu as dit uuid -> ok (string)
            $incident->assigned_by = $actor->id;
            $incident->assigned_at = now();
            $incident->save();

            $this->audit(
                action: 'incident_assigned',
                modelType: 'incident',
                modelId: (string) $incident->id,
                ipAddress: $ipAddress,
                actor: $actor,
                meta: array_merge([
                    'assigned_to' => (string) $superviseur->id,
                    'assigned_by' => (string) $actor->id,
                    'assigned_at' => now()->toDateTimeString(),
                ], $payload)
            );

            $this->notifyAssigned($incident, $superviseur, $actor);

            return $incident;
        });
    }

    /**
     * Valide un incident + audit.
     */
    public function validateIncident(Incident $incident, User $actor, string $ipAddress): Incident
    {
        return DB::transaction(function () use ($incident, $actor, $ipAddress) {

            if ($this->isLocked($incident)) {
                throw new BusinessRuleException("Incident clôturé/archivé : validation impossible.");
            }

            if ($actor->user_role === 'moniteur') {
                throw new BusinessRuleException("Un moniteur ne peut pas valider.");
            }

            // Scope province
            if ($actor->user_role !== 'superadmin' && $actor->code_province !== $incident->code_province) {
                throw new BusinessRuleException("Accès refusé.");
            }

            // Si pas superadmin: doit être superviseur assigné
            if ($actor->user_role !== 'superadmin') {
                if (!$incident->assigned_to) {
                    throw new BusinessRuleException("Veuillez d'abord assigner l'incident à un superviseur.");
                }
                if ((string)$incident->assigned_to !== (string)$actor->id) {
                    throw new BusinessRuleException("Seul le superviseur assigné peut valider cet incident.");
                }
            }

            if ($incident->statut_incident === 'Validé') {
                return $incident; // déjà validé
            }

            if ($incident->statut_incident !== 'En attente') {
                throw new BusinessRuleException("Seul un incident en attente peut être validé.");
            }

            if ($incident->violences()->count() === 0) {
                throw new BusinessRuleException("Impossible de valider : l'incident doit posséder au moins une violence documentée.");
            }

            $incident->statut_incident = 'Validé';
            $incident->last_status_changed_at = now();
            $incident->save();

            $this->audit(
                action: 'incident_validated',
                modelType: 'incident',
                modelId: (string) $incident->id,
                ipAddress: $ipAddress,
                actor: $actor,
                meta: [
                    'validated_by' => (string) $actor->id,
                    'validated_at' => now()->toDateTimeString(),
                ]
            );

            return $incident;
        });
    }

    /**
     * Archive un incident + audit.
     */
    public function archiveIncident(Incident $incident, User $actor, string $ipAddress): Incident
    {
        return DB::transaction(function () use ($incident, $actor, $ipAddress) {

            // moniteur ne peut pas archiver
            if ($actor->user_role === 'moniteur') {
                throw new BusinessRuleException("Un moniteur ne peut pas archiver.");
            }

            // Scope province
            if ($actor->user_role !== 'superadmin' && $actor->code_province !== $incident->code_province) {
                throw new BusinessRuleException("Accès refusé.");
            }

            if ($incident->statut_incident === 'Archivé') {
                return $incident;
            }

            if ($incident->statut_incident === 'Cloturée') {
                throw new BusinessRuleException("Incident clôturé : archivage non autorisé.");
            }

            $incident->statut_incident = 'Archivé';
            $incident->last_status_changed_at = now();
            $incident->save();

            $this->audit(
                action: 'incident_archived',
                modelType: 'incident',
                modelId: (string) $incident->id,
                ipAddress: $ipAddress,
                actor: $actor,
                meta: [
                    'archived_by' => (string) $actor->id,
                    'archived_at' => now()->toDateTimeString(),
                ]
            );

            return $incident;
        });
    }

    // -------------------------
    // Helpers
    // -------------------------

    private function isLocked(Incident $incident): bool
    {
        return in_array($incident->statut_incident, ['Cloturée', 'Archivé'], true);
    }

    private function audit(string $action, string $modelType, string $modelId, string $ipAddress, User $actor, array $meta = []): void
    {
        AuditLog::create([
            // selon ton script audit_logs.id est integer primary key
            // 'id' => random_int(100000000, 999999999),
            'user_id' => $actor->id,
            'user_action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId, // uuid
            'ip_address' => $ipAddress, // string (tu as déjà corrigé)
            'action_meta' => json_encode($meta, JSON_UNESCAPED_UNICODE),
        ]);
    }

    private function nextIncidentCode(): string
    {
        // suppose que la séquence existe: incident_code_seq
        $row = DB::selectOne("SELECT nextval('incident_code_seq') as n");
        $n = (int) ($row->n ?? 1);

        return 'ALT-' . str_pad((string) $n, 6, '0', STR_PAD_LEFT);
    }

    private function notifySuperviseursNeedsValidation(Incident $incident): void
    {
        $superviseurs = User::query()
            ->where('is_active', true)
            ->where('user_role', 'superviseur')
            ->where('code_province', $incident->code_province)
            ->get();

        if ($superviseurs->isEmpty()) {
            return;
        }

        $provinceName = DB::table('provinces')->where('code_province', $incident->code_province)->where('is_active', 'YES')->value('nom_province') ?? '-';
        $territoireName = $incident->code_territoire
            ? (DB::table('territoires')->where('code_territoire', $incident->code_territoire)->value('nom_territoire') ?? '-')
            : '-';
        $zoneName = $incident->code_zonesante
            ? (DB::table('zonesantes')->where('code_zonesante', $incident->code_zonesante)->value('nom_zonesante') ?? '-')
            : '-';

        foreach ($superviseurs as $sup) {
            if (!$sup->email) continue;

            Mail::to($sup->email)->send(
                new IncidentNeedsValidationMail(
                    incident: $incident,
                    userName: $sup->name ?? 'Superviseur',
                    province: $provinceName,
                    territoire: $territoireName,
                    zoneSante: $zoneName
                )
            );
        }
    }

    private function notifyAssigned(Incident $incident, User $superviseur, User $assignedBy): void
    {
        if (!$superviseur->email) return;

        $provinceName = DB::table('provinces')->where('code_province', $incident->code_province)->where('is_active', 'YES')->value('nom_province') ?? '-';
        $territoireName = $incident->code_territoire
            ? (DB::table('territoires')->where('code_territoire', $incident->code_territoire)->value('nom_territoire') ?? '-')
            : '-';
        $zoneName = $incident->code_zonesante
            ? (DB::table('zonesantes')->where('code_zonesante', $incident->code_zonesante)->value('nom_zonesante') ?? '-')
            : '-';

        $actionUrl = route('incidents.show', $incident->id);

        Mail::to($superviseur->email)->send(
            new IncidentAssignedMail(
                incident: $incident,
                superviseurName: $superviseur->name ?? 'Superviseur',
                assignedByName: $assignedBy->name ?? 'Administrateur',
                provinceName: $provinceName,
                territoireName: $territoireName,
                zoneName: $zoneName,
                actionUrl: $actionUrl
            )
        );
    }

    private function notifySupervisorsOfNewIncident(Incident $incident): void
    {
        $supervisors = User::where('user_role', 'superviseur')
            ->where('code_province', $incident->code_province)
            ->where('is_active', true)
            ->get();

        if ($supervisors->isEmpty()) {
            return;
        }

        $provinceName = $incident->province?->nom_province ?? '-';
        $eventType = $incident->evenement?->nom_evenement ?? '-';
        $reportingOrg = $incident->creator?->organisation?->name ?? 'Indépendante';

        foreach ($supervisors as $supervisor) {
            Mail::to($supervisor->email)->send(new NewIncidentNotificationMail(
                $incident,
                $reportingOrg,
                $eventType,
                $provinceName
            ));
        }
    }
}

