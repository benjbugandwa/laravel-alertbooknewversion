<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class IncidentsWorkbookExport implements WithMultipleSheets
{
    public function __construct(
        public string $from,
        public string $to,
        public ?string $province,
        public bool $includeSurvivantName,
        public bool $includeNotes,
        public bool $includeReferencements,
        public bool $includeViolences,
    ) {}

    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new Sheets\IncidentsSheet(
            from: $this->from,
            to: $this->to,
            province: $this->province,
            includeSurvivantName: $this->includeSurvivantName,
            includeNotes: $this->includeNotes,
            includeViolences: $this->includeViolences,
        );

        if ($this->includeReferencements) {
            $sheets[] = new Sheets\ReferencementsSheet(
                from: $this->from,
                to: $this->to,
                province: $this->province,
            );
        }

        return $sheets;
    }
}
