<?php

namespace App\Exports;

use App\Models\Incident;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class IncidentsMatrixExport implements FromCollection, WithHeadings
{
    public function __construct(
        public string $from,
        public string $to,
        public ?string $province,
        public bool $includeSurvivantName = false,
    ) {}

    public function headings(): array
    {
        return [
            'code_incident',
            'date_incident',
            'province',
            'zone_sante',
            'localite',
            'severite',
            'statut_incident',
            'survivant', // nom ou code
            'violences',
            'referencements',
            'notes',
        ];
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
                'violences',
                'referencements.provider',
                'caseNotes.author',
            ]);

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

            $violences = ($inc->violences ?? collect())
                ->map(fn($v) => $v->violence_name)
                ->implode(' | ');

            $refs = ($inc->referencements ?? collect())
                ->map(function ($r) {
                    $p = $r->provider;
                    return ($r->code_referencement ?? '-') .
                        ' - ' . ($r->type_reponse ?? '-') .
                        ' - ' . ($r->statut_reponse ?? '-') .
                        ' - ' . ($p->provider_name ?? '-') .
                        ' (' . ($p->focalpoint_number ?? '-') . ')';
                })->implode(' || ');

            $notes = ($inc->caseNotes ?? collect())
                ->sortBy('created_at')
                ->map(function ($n) {
                    $by = $n->author?->name ?? '-';
                    $dt = optional($n->created_at)->format('Y-m-d');
                    return $dt . ' - ' . $by . ': ' . str($n->case_note)->limit(120);
                })->implode(' || ');

            return [
                $inc->code_incident,
                optional($inc->date_incident)->format('Y-m-d'),
                $inc->province->nom_province ?? $inc->code_province,
                $inc->zoneSante->nom_zonesante ?? $inc->code_zonesante,
                $inc->localite,
                $inc->severite,
                $inc->statut_incident,
                $survivant,
                $violences,
                $refs,
                $notes,
            ];
        });
    }
}
