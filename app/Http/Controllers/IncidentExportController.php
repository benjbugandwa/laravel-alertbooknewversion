<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\IncidentsWorkbookExport;
//use App\Exports\IncidentsWorkbookExport;

class IncidentExportController extends Controller
{
    public function export(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'from' => ['required', 'date'],
            'to' => ['required', 'date', 'after_or_equal:from'],
            'format' => ['required', 'in:csv,xlsx'],
            'province' => ['nullable', 'string'],

            'include_notes' => ['nullable', 'in:0,1'],
            'include_referencements' => ['nullable', 'in:0,1'],
            'include_violences' => ['nullable', 'in:0,1'],
        ]);

        if ($user->user_role !== 'superadmin') {
            $data['province'] = $user->code_province;
        }

        $includeNotes = (bool) ((int) ($data['include_notes'] ?? 0));
        $includeRefs = (bool) ((int) ($data['include_referencements'] ?? 0));
        $includeViolences = (bool) ((int) ($data['include_violences'] ?? 0));

        // CSV = 1 feuille => on ignore "refs" (ou on force xlsx)
        if ($data['format'] === 'csv') {
            $includeRefs = false;
        }

        $export = new IncidentsWorkbookExport(
            from: $data['from'],
            to: $data['to'],
            province: $data['province'] ?? null,
            includeSurvivantName: ($user->user_role === 'superadmin'),
            includeNotes: $includeNotes,
            includeReferencements: $includeRefs,
            includeViolences: $includeViolences,
        );

        $ext = $data['format'];
        $filename = 'Incidents-Matrice-' . $data['from'] . '_to_' . $data['to'] . '.' . $ext;

        return Excel::download($export, $filename);
    }
}
