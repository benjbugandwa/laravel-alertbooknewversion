<?php

namespace App\Exports\Sheets;

use App\Models\Referencement;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReferencementsSheet implements FromCollection, WithHeadings
{
    public function __construct(
        public string $from,
        public string $to,
        public ?string $province,
    ) {}

    public function headings(): array
    {
        return [
            'incident_code',
            'referencement_code',
            'date_referencement',
            'type_reponse',
            'statut_reponse',
            'provider_name',
            'provider_location',
            'focalpoint_name',
            'focalpoint_number',
            'resultat',
            'observations',
        ];
    }

    public function collection(): Collection
    {
        $q = Referencement::query()
            ->whereBetween('date_referencement', [$this->from, $this->to])
            ->with(['incident.province', 'provider']);

        if ($this->province) {
            $q->whereHas('incident', fn($qq) => $qq->where('code_province', $this->province));
        }

        return $q->get()->map(function ($r) {
            $p = $r->provider;

            return [
                $r->incident?->code_incident ?? '-',
                $r->code_referencement,
                optional($r->date_referencement)->format('Y-m-d'),
                $r->type_reponse,
                $r->statut_reponse,
                $p?->provider_name ?? '-',
                $p?->provider_location ?? '-',
                $p?->focalpoint_name ?? '-',
                $p?->focalpoint_number ?? '-',
                $r->resultat,
                $r->observations,
            ];
        });
    }
}
