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
        ]);

        $pdf = Pdf::loadView('pdf.incident', [
            'incident' => $incident,
            'generatedBy' => $user,
            'generatedAt' => now(),
        ])->setPaper('a4', 'portrait');

        // Nom fichier
        $filename = 'Fiche-Incident-' . $incident->code_incident . '.pdf';

        return $pdf->download($filename);
    }
}
