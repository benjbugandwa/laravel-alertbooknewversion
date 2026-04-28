<?php

namespace App\Exports\Sheets;

use App\Models\Incident;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class IncidentsSheet implements FromCollection, WithHeadings
{
    public function __construct(
        public string $from,
        public string $to,
        public ?string $province,
        public bool $includeSurvivantName, // Conservé pour la compatibilité avec le constructeur de l'export principal
        public bool $includeNotes,
        public bool $includeViolences,
    ) {}

    public function headings(): array
    {
        $cols = [
            'code_incident',
            'date_incident',
            'province',
            'nom_chefferie',
            'nom_groupement',
            'zone_sante',
            'nom_airesante',
            'localite',
            'code_evenement',
            'nom_evenement',
            'description_faits',
            'created_by',
            'source_info',
            'auteur_presume',
            'severite',
            'statut_incident',
        ];

        if ($this->includeViolences) {
            $cols[] = 'violences';
        }

        if ($this->includeNotes) {
            $cols[] = 'notes';
        }

        return $cols;
    }

    public function collection(): Collection
    {
        $q = Incident::query()
            ->whereBetween('date_incident', [$this->from, $this->to])
            ->where('statut_incident', 'Validé') // Uniquement les incidents validés
            ->with([
                'province',
                'zoneSante',
                'chefferie',
                'groupement',
                'aireSante',
                'evenement',
                'creator', // Pour récupérer le nom du créateur
            ]);

        if ($this->includeViolences) {
            $q->with('violences');
        }

        if ($this->includeNotes) {
            $q->with(['caseNotes.author']);
        }

        if ($this->province) {
            $q->where('code_province', $this->province);
        }

        return $q->get()->flatMap(function ($inc) {
            $baseRow = [
                $inc->code_incident,
                optional($inc->date_incident)->format('Y-m-d'),
                $inc->province->nom_province ?? $inc->code_province,
                $inc->chefferie->nom_chefferie ?? $inc->code_chefferie,
                $inc->groupement->nom_groupement ?? $inc->code_groupement,
                $inc->zoneSante->nom_zonesante ?? $inc->code_zonesante,
                $inc->aireSante->nom_airesante ?? $inc->code_airesante,
                $inc->localite,
                $inc->evenement->code_evenement ?? $inc->code_evenement,
                $inc->evenement->nom_evenement ?? '-',
                $inc->description_faits,
                $inc->creator->name ?? $inc->created_by ?? '-',
                $inc->source_info,
                $inc->auteur_presume,
                $inc->severite,
                $inc->statut_incident,
            ];

            $violences = ($this->includeViolences && $inc->violences) ? $inc->violences : collect();

            $notes = '';
            if ($this->includeNotes) {
                $notes = ($inc->caseNotes ?? collect())
                    ->sortBy('created_at')
                    ->map(function ($n) {
                        $by = $n->author?->name ?? '-';
                        $dt = optional($n->created_at)->format('Y-m-d');
                        return $dt . ' - ' . $by . ': ' . str($n->case_note)->limit(140);
                    })
                    ->implode(' || ');
            }

            // Si aucune violence ou violences non incluses
            if ($violences->isEmpty()) {
                $row = $baseRow;
                if ($this->includeViolences) {
                    $row[] = '-'; // empty violence
                }
                if ($this->includeNotes) {
                    $row[] = $notes;
                }
                return [$row];
            }

            // Si plusieurs violences, on duplique la ligne
            $rows = [];
            foreach ($violences as $v) {
                $row = $baseRow;
                if ($this->includeViolences) {
                    $row[] = $v->violence_name;
                }
                if ($this->includeNotes) {
                    $row[] = $notes;
                }
                $rows[] = $row;
            }

            return $rows;
        });
    }
}
