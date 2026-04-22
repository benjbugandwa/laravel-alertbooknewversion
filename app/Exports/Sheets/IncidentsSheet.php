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
        public bool $includeSurvivantName,
        public bool $includeNotes,
        public bool $includeViolences,
    ) {}

    public function headings(): array
    {
        $cols = [
            'code_incident',
            'date_incident',
            'province',
            'zone_sante',
            'localite',
            'severite',
            'statut_incident',
            'survivant',
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
            ->where('statut_incident', '!=', 'Archivé')
            ->with([
                'province',
                'zoneSante',
                'survivant',
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

        return $q->get()->map(function ($inc) {
            $survivant = '-';
            if ($inc->survivant) {
                $survivant = $this->includeSurvivantName
                    ? ($inc->survivant->full_name ?? $inc->survivant->code_survivant)
                    : ($inc->survivant->code_survivant ?? '-');
            }

            $row = [
                $inc->code_incident,
                optional($inc->date_incident)->format('Y-m-d'),
                $inc->province->nom_province ?? $inc->code_province,
                $inc->zoneSante->nom_zonesante ?? $inc->code_zonesante,
                $inc->localite,
                $inc->severite,
                $inc->statut_incident,
                $survivant,
            ];

            if ($this->includeViolences) {
                $row[] = ($inc->violences ?? collect())
                    ->map(fn($v) => $v->violence_name)
                    ->implode(' | ');
            }

            if ($this->includeNotes) {
                $row[] = ($inc->caseNotes ?? collect())
                    ->sortBy('created_at')
                    ->map(function ($n) {
                        $by = $n->author?->name ?? '-';
                        $dt = optional($n->created_at)->format('Y-m-d');
                        return $dt . ' - ' . $by . ': ' . str($n->case_note)->limit(140);
                    })
                    ->implode(' || ');
            }

            return $row;
        });
    }
}
