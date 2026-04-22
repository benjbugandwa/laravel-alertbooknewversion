<?php

namespace App\Livewire\Pages\Supervision;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

class SuperviseurPerformance extends Component
{
    public string $province = '';          // code_province (superadmin seulement)
    public string $superviseurId = '';     // users.id (string pour supporter int/uuid)
    public ?string $dateFrom = null;       // optionnel
    public ?string $dateTo = null;         // optionnel

    public function mount(): void
    {
        // Admin → province forcée
        if (!$this->isSuperAdmin()) {
            $this->province = Auth::user()->code_province ?? '';
        }
    }

    private function isSuperAdmin(): bool
    {
        return Auth::user()?->user_role === 'superadmin';
    }

    public function updatedProvince(): void
    {
        // Quand superadmin change province: reset superviseur
        $this->superviseurId = '';
    }

    #[Computed]
    public function provinces(): array
    {
        return DB::table('provinces')
            ->select('code_province', 'nom_province')
            ->orderBy('nom_province')
            ->get()
            ->map(fn($p) => ['code' => $p->code_province, 'name' => $p->nom_province])
            ->toArray();
    }

    #[Computed]
    public function superviseurs(): array
    {
        $q = DB::table('users')
            ->where('is_active', true)
            ->where('user_role', 'superviseur');

        // Admin -> seulement sa province ; Superadmin -> province filtrable si choisie
        if (!$this->isSuperAdmin()) {
            $q->where('code_province', Auth::user()->code_province);
        } else {
            if ($this->province !== '') {
                $q->where('code_province', $this->province);
            }
        }

        return $q
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'code_province'])
            ->map(function ($u) {
                return [
                    'id' => (string) $u->id,
                    'name' => $u->name,
                    'email' => $u->email,
                    'code_province' => $u->code_province,
                ];
            })
            ->toArray();
    }

    private function applyDateRangeToIncidentQuery($query)
    {
        // On filtre sur incidents.date_incident si fourni
        if ($this->dateFrom) {
            $query->whereDate('incidents.date_incident', '>=', $this->dateFrom);
        }
        if ($this->dateTo) {
            $query->whereDate('incidents.date_incident', '<=', $this->dateTo);
        }
        return $query;
    }

    public function render()
    {
        $kpiAssigned = 0;
        $kpiValidated = 0;
        $kpiPending = 0;
        $pctValidated = 0.0;
        $recentNotes = [];
        $recentReferencements = [];

        if ($this->superviseurId !== '') {
            // ---- Incidents assignés / validés / en attente ----
            $assignedQuery = DB::table('incidents')
                ->where('incidents.statut_incident', '!=', 'Archivé')
                ->where('incidents.assigned_to', $this->superviseurId);

            $assignedQuery = $this->applyDateRangeToIncidentQuery($assignedQuery);

            // Admin: double sécurité → province scope
            if (!$this->isSuperAdmin()) {
                $assignedQuery->where('incidents.code_province', Auth::user()->code_province);
            } else {
                if ($this->province !== '') {
                    $assignedQuery->where('incidents.code_province', $this->province);
                }
            }

            $kpiAssigned = (int) (clone $assignedQuery)->count();

            $kpiValidated = (int) (clone $assignedQuery)
                ->where('incidents.statut_incident', 'Validé')
                ->count();

            $kpiPending = (int) (clone $assignedQuery)
                ->where('incidents.statut_incident', 'En attente')
                ->count();

            $pctValidated = $kpiAssigned > 0
                ? round(($kpiValidated / $kpiAssigned) * 100, 1)
                : 0.0;

            // ---- Notes récentes du superviseur ----
            $notesQuery = DB::table('case_notes')
                ->leftJoin('incidents', 'case_notes.id_incident', '=', 'incidents.id')
                ->where('case_notes.created_by', $this->superviseurId);

            if (!$this->isSuperAdmin()) {
                $notesQuery->where('incidents.code_province', Auth::user()->code_province);
            } else {
                if ($this->province !== '') {
                    $notesQuery->where('incidents.code_province', $this->province);
                }
            }

            $recentNotes = $notesQuery
                ->orderByDesc('case_notes.created_at')
                ->limit(10)
                ->get([
                    'case_notes.id',
                    'case_notes.id_incident',
                    'case_notes.is_confidential',
                    'case_notes.created_at',
                    DB::raw("LEFT(case_notes.case_note, 160) as excerpt"),
                    'incidents.code_incident',
                ])
                ->map(fn($n) => [
                    'id' => $n->id,
                    'id_incident' => $n->id_incident,
                    'code_incident' => $n->code_incident,
                    'is_confidential' => (bool) $n->is_confidential,
                    'created_at' => $n->created_at,
                    'excerpt' => $n->excerpt,
                ])
                ->toArray();

            // ---- Référencements récents du superviseur ----
            $refQuery = DB::table('referencements')
                ->leftJoin('incidents', 'referencements.id_incident', '=', 'incidents.id')
                ->leftJoin('service_providers', 'referencements.provider_id', '=', 'service_providers.id')
                ->where('referencements.created_by', $this->superviseurId);

            if (!$this->isSuperAdmin()) {
                $refQuery->where('incidents.code_province', Auth::user()->code_province);
            } else {
                if ($this->province !== '') {
                    $refQuery->where('incidents.code_province', $this->province);
                }
            }

            $recentReferencements = $refQuery
                ->orderByDesc('referencements.created_at')
                ->limit(10)
                ->get([
                    'referencements.id',
                    'referencements.id_incident',
                    'referencements.code_referencement',
                    'referencements.type_reponse',
                    'referencements.statut_reponse',
                    'referencements.created_at',
                    'incidents.code_incident',
                    'service_providers.provider_name',
                    'service_providers.focalpoint_number',
                ])
                ->map(fn($r) => [
                    'id' => $r->id,
                    'id_incident' => $r->id_incident,
                    'code_incident' => $r->code_incident,
                    'code_referencement' => $r->code_referencement,
                    'type_reponse' => $r->type_reponse,
                    'statut_reponse' => $r->statut_reponse,
                    'provider_name' => $r->provider_name,
                    'focalpoint_number' => $r->focalpoint_number,
                    'created_at' => $r->created_at,
                ])
                ->toArray();
        }

        // Pour afficher nom de province du superviseur sélectionné (optionnel)
        $selected = collect($this->superviseurs)->firstWhere('id', $this->superviseurId);

        return view('livewire.pages.supervision.superviseur-performance', [
            'selectedSuperviseur' => $selected,
            'kpiAssigned' => $kpiAssigned,
            'kpiValidated' => $kpiValidated,
            'kpiPending' => $kpiPending,
            'pctValidated' => $pctValidated,
            'recentNotes' => $recentNotes,
            'recentReferencements' => $recentReferencements,
        ]);
    }
}
