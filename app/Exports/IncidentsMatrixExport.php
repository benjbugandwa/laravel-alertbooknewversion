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
            'nom_chefferie',
            'nom_groupement',
            'zone_sante',
            'nom_airesante',
            'localite',
            'code_evenement',
            'nom_evenement',
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
                'chefferie',
                'groupement',
                'aireSante',
                'evenement',
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
                $inc->chefferie->nom_chefferie ?? $inc->code_chefferie,
                $inc->groupement->nom_groupement ?? $inc->code_groupement,
                $inc->zoneSante->nom_zonesante ?? $inc->code_zonesante,
                $inc->aireSante->nom_airesante ?? $inc->code_airesante,
                $inc->localite,
                $inc->evenement->code_evenement ?? $inc->code_evenement,
                $inc->evenement->nom_evenement ?? '-',
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
