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
    /**
     * Reçoit et enregistre un incident depuis l'application mobile.
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'id' => 'required|string', // UUID local du mobile (pour éviter les doublons en cas de retry)
                'code_incident' => 'required|string', // ALT-000000
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
                'photo_url' => 'nullable|string', // Si c'est en base64
                'created_at' => 'required|date',
            ]);

            // Vérifier si cet incident exact a déjà été synchronisé (via son UUID généré hors-ligne)
            $existingById = Incident::find($data['id']);
            if ($existingById) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Incident déjà synchronisé',
                    'incident' => $existingById
                ], 200);
            }

            // Vérifier si le code_incident existe déjà (généré par un autre téléphone)
            $existingByCode = Incident::where('code_incident', $data['code_incident'])->exists();
            
            $finalCode = $data['code_incident'];
            if ($existingByCode) {
                // Obtenir le code le plus grand (ça marche bien car ils sont formatés avec des zéros: ALT-000001)
                $maxCode = Incident::where('code_incident', 'like', 'ALT-%')->max('code_incident');
                $nextNumber = 1;
                if ($maxCode) {
                    $nextNumber = intval(substr($maxCode, 4)) + 1;
                }
                $finalCode = 'ALT-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
            }

            // Gestion de l'image Base64
            $photoPath = null;
            if (!empty($data['photo_url'])) {
                // Décodage et enregistrement (A adapter selon le format reçu du mobile, ex: base64)
                if (preg_match('/^data:image\/(\w+);base64,/', $data['photo_url'], $type)) {
                    $data['photo_url'] = substr($data['photo_url'], strpos($data['photo_url'], ',') + 1);
                    $type = strtolower($type[1]); // jpg, png, gif
                    
                    if (in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
                        $image = base64_decode($data['photo_url']);
                        $imageName = 'incidents/' . $finalCode . '_' . time() . '.' . $type;
                        Storage::disk('public')->put($imageName, $image);
                        $photoPath = $imageName;
                    }
                }
            }

            // Création de l'incident
            $incident = new Incident();
            $incident->id = $data['id']; // On garde l'UUID du mobile !
            $incident->code_incident = $finalCode;
            $incident->created_by = $request->user()->id; // L'utilisateur connecté via Sanctum
            $incident->severite = $data['severite'];
            $incident->statut_incident = 'En attente';
            $incident->auteur_presume = $data['auteur_presume'] ?? null;
            $incident->code_province = $data['code_province'] ?? null;
            $incident->code_territoire = $data['code_territoire'] ?? null;
            $incident->code_chefferie = $data['code_chefferie'] ?? null;
            $incident->code_groupement = $data['code_groupement'] ?? null;
            $incident->code_zonesante = $data['code_zonesante'] ?? null;
            $incident->code_airesante = $data['code_airesante'] ?? null;
            $incident->localite = $data['localite'] ?? null;
            $incident->description_faits = $data['description_faits'];
            $incident->source_info = $data['source_info'] ?? null;
            $incident->longitude = $data['longitude'] ?? null;
            $incident->latitude = $data['latitude'] ?? null;
            $incident->code_evenement = $data['code_evenement'] ?? null;
            $incident->photo_url = $photoPath;
            // date_incident est la date sélectionnée (où l'incident a eu lieu)
            $incident->date_incident = $data['created_at']; 
            // created_at sera automatiquement géré par Laravel (date du jour d'envoi dans la BDD) 
            $incident->save();

            // La table audit_logs devrait se remplir automatiquement si vous avez des Observers sur le modèle Incident.
            // Si ce n'est pas le cas, on l'ajoute manuellement :
            /*
            AuditLog::create([
                'user_id' => $request->user()->id,
                'user_action' => 'Création via Mobile',
                'model_type' => Incident::class,
                'ip_address' => $request->ip(),
                'action_meta' => json_encode(['source' => 'mobile_app', 'local_id' => $data['id']]),
                'model_id' => $incident->id,
            ]);
            */

            return response()->json([
                'status' => 'success',
                'message' => 'Incident synchronisé',
                'incident' => $incident
            ], 201);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la synchro mobile : ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur de synchronisation'
            ], 500);
        }
    }
}
