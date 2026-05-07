<?php

namespace App\Exports;

use App\Models\Mouvement;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MovementsExport implements FromQuery, WithMapping, WithHeadings, WithStyles, ShouldAutoSize
{
    use Exportable;

    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Mouvement::query()
            ->with(['incident.evenement', 'territoireProv', 'territoireAccl', 'zoneSanteProv', 'zoneSanteAccl']);

        if (!empty($this->filters['start_date'])) {
            $query->where('date_mouvement', '>=', $this->filters['start_date']);
        }

        if (!empty($this->filters['end_date'])) {
            $query->where('date_mouvement', '<=', $this->filters['end_date']);
        }

        if (!empty($this->filters['territoire'])) {
            $query->where('code_territoire_accl', $this->filters['territoire']);
        }

        if (!empty($this->filters['zonesante'])) {
            $query->where('code_zonesante_accl', $this->filters['zonesante']);
        }

        return $query->orderBy('date_mouvement', 'desc');
    }

    public function headings(): array
    {
        return [
            'Code Incident',
            'Date',
            'Type d\'événement / Cause',
            'Provenance (Territoire)',
            'Provenance (Zone de Santé)',
            'Provenance (Localité)',
            'Accueil (Territoire)',
            'Accueil (Zone de Santé)',
            'Accueil (Localité)',
            'Nombre de ménages',
            'Nombre d\'individus',
            'Type de logement',
            'Source d\'information',
            'Remarques',
        ];
    }

    public function map($mouvement): array
    {
        $cause = $mouvement->incident_id 
            ? ($mouvement->incident->evenement->nom_evenement ?? 'Inconnu') 
            : ($mouvement->cause_deplacement ?? 'Non spécifiée');

        return [
            $mouvement->incident->code_incident ?? 'NULL',
            $mouvement->date_mouvement->format('d/m/Y'),
            $cause,
            $mouvement->territoireProv->nom_territoire ?? '-',
            $mouvement->zoneSanteProv->nom_zonesante ?? '-',
            $mouvement->localite_prov,
            $mouvement->territoireAccl->nom_territoire ?? '-',
            $mouvement->zoneSanteAccl->nom_zonesante ?? '-',
            $mouvement->localite_accl,
            $mouvement->estim_nbre_menages,
            $mouvement->estim_nbre_personnes,
            $mouvement->type_logement ?? '-',
            $mouvement->source_info,
            $mouvement->remarques_mouvement,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E6F0FA']]],
        ];
    }
}
