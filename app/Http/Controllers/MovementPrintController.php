<?php

namespace App\Http\Controllers;

use App\Models\Mouvement;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MovementPrintController extends Controller
{
    public function show($id)
    {
        $mouvement = Mouvement::with([
            'creator',
            'incident.evenement',
            'provinceProv',
            'territoireProv',
            'zoneSanteProv',
            'provinceAccl',
            'territoireAccl',
            'zoneSanteAccl'
        ])->findOrFail($id);

        $user = Auth::user();

        $pdf = Pdf::loadView('pdf.mouvement', [
            'mouvement' => $mouvement,
            'generatedBy' => $user,
            'generatedAt' => now(),
        ])->setPaper('a4', 'portrait')
          ->setOptions([
              'isRemoteEnabled' => true,
              'isHtml5ParserEnabled' => true,
          ]);

        $filename = 'Fiche-Mouvement-' . ($mouvement->incident->code_incident ?? 'Standalone') . '-' . $mouvement->id . '.pdf';

        return $pdf->download($filename);
    }
}
