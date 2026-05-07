<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class IncidentPrintController extends Controller
{
    public function show(Request $request, Incident $incident)
    {
        $user = $request->user();

        // Autorisation: superadmin voit tout; sinon seulement sa province
        if ($user->user_role !== 'superadmin' && $incident->code_province !== $user->code_province) {
            abort(403);
        }

        // Charger tout ce qu’on doit afficher dans la fiche
        $incident->load([
            'province',      // relation Province (code_province)
            'territoire',    // relation Territoire (code_territoire)
            'chefferie',     // relation Chefferie (code_chefferie)
            'groupement',    // relation Groupement (code_groupement)
            'zoneSante',     // relation ZoneSante (code_zonesante)
            'aireSante',     // relation AireSante (code_airesante)
            'evenement',     // relation Evenement (code_evenement)
            'assignedTo',    // relation user superviseur assigné (si tu as assigned_to)
            'violences',     // pivot violence_incidents
            'referencements.provider', // provider pour focal point
            'mouvements.territoireProv',
            'mouvements.territoireAccl',
        ]);

        // Préparer la carte en base64 (plus fiable pour DomPDF)
        $mapBase64 = null;
        if ($incident->latitude && $incident->longitude) {
            $lon = (float) $incident->longitude;
            $lat = (float) $incident->latitude;
            // Yandex Static Maps
            $mapUrl = "https://static-maps.yandex.ru/1.x/?ll={$lon},{$lat}&z=13&l=map&pt={$lon},{$lat},pm2rdm&size=600,300";
            
            try {
                $context = stream_context_create([
                    "ssl" => ["verify_peer" => false, "verify_peer_name" => false],
                ]);
                $mapData = @file_get_contents($mapUrl, false, $context);
                if ($mapData) {
                    $mapBase64 = 'data:image/png;base64,' . base64_encode($mapData);
                }
            } catch (\Exception $e) {
                // On laisse mapBase64 à null
            }
        }

        $pdf = Pdf::loadView('pdf.incident', [
            'incident' => $incident,
            'generatedBy' => $user,
            'generatedAt' => now(),
            'mapBase64' => $mapBase64,
        ])->setPaper('a4', 'portrait')
          ->setOptions([
              'isRemoteEnabled' => true,
              'isHtml5ParserEnabled' => true,
          ]);

        // Nom fichier
        $filename = 'Fiche-Incident-' . $incident->code_incident . '.pdf';

        return $pdf->download($filename);
    }
}
