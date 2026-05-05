<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Incident;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class IncidentController extends Controller
{
    protected $incidentService;

    public function __construct(\App\Services\IncidentService $incidentService)
    {
        $this->incidentService = $incidentService;
    }

    /**
     * Reçoit et enregistre un incident depuis l'application mobile.
     */
    public function store(Request $request)
    {
        Log::info('Requête de synchronisation reçue', [
            'user' => $request->user()?->id,
            'payload_id' => $request->input('id'),
            'ip' => $request->ip()
        ]);
        try {
            $data = $request->validate([
                'id' => 'required|string', // UUID local du mobile (utilisé pour éviter les doublons)
                'severite' => 'nullable|string',
                'auteur_presume' => 'nullable|string',
                'code_province' => 'nullable|string',
                'code_territoire' => 'nullable|string',
                'code_chefferie' => 'nullable|string',
                'code_groupement' => 'nullable|string',
                'code_zonesante' => 'nullable|string',
                'code_airesante' => 'nullable|string',
                'localite' => 'nullable|string',
                'description_faits' => 'required|string',
                'source_info' => 'nullable|string',
                'longitude' => 'nullable|numeric',
                'latitude' => 'nullable|numeric',
                'code_evenement' => 'nullable|string',
                'photo_url' => 'nullable|string', // Base64
                'created_at' => 'required|date',
            ]);

            // Vérifier si cet incident (par son UUID mobile) existe déjà sur le serveur
            $existing = Incident::where('id', $data['id'])->first();
            if ($existing) {
                return response()->json([
                    'message' => 'Incident déjà synchronisé.',
                    'incident' => $existing
                ], 200);
            }

            // Préparation du payload pour le service
            $payload = $data;
            // date_incident est la date de création sur le mobile
            $payload['date_incident'] = $data['created_at'];
            $payload['statut_incident'] = 'En attente';

            // Gestion de la photo (Base64 -> Fichier physique)
            $photoPath = null;
            if (!empty($data['photo_url'])) {
                if (preg_match('/^data:image\/(\w+);base64,/', $data['photo_url'], $type)) {
                    $base64Data = substr($data['photo_url'], strpos($data['photo_url'], ',') + 1);
                    $type = strtolower($type[1]);
                    if (in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
                        $imageContent = base64_decode($base64Data);
                        $imageName = 'incidents/sync_' . uniqid() . '.' . $type;
                        Storage::disk('public')->put($imageName, $imageContent);
                        $photoPath = $imageName;
                    }
                }
            }

            // Utilisation du service pour garantir la cohérence (Code, Province, Notifications, Audit)
            // On passe null pour UploadedFile car on a géré le stockage manuellement pour le base64
            // On injectera le photo_url dans le payload
            if ($photoPath) {
                $payload['photo_url'] = $photoPath;
            }

            $incident = $this->incidentService->create($payload, null, $request->user(), $request->ip());

            return response()->json([
                'status' => 'success',
                'message' => 'Incident synchronisé',
                'incident' => $incident
            ], 201);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la synchro mobile : ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur de synchronisation : ' . $e->getMessage()
            ], 500);
        }
    }
}
