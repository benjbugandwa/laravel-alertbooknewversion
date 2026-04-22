<?php

namespace App\Livewire\Pages\Superviseurs;

use App\Models\Incident;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Performance extends Component
{
    public ?int $superviseurId = null;

    public string $rangeStart; // YYYY-MM-DD
    public string $rangeEnd;   // YYYY-MM-DD

    public function mount(): void
    {
        $this->authorizeAccess();

        $this->rangeEnd = now()->toDateString();
        $this->rangeStart = now()->subMonths(3)->toDateString();

        // Sélection par défaut : le 1er superviseur visible
        $sups = $this->superviseurs();
        if (!$this->superviseurId && !empty($sups)) {
            $this->superviseurId = (int) $sups[0]['id'];
        }

        $this->dispatchTrendUpdate();
    }

    private function authorizeAccess(): void
    {
        $role = Auth::user()->user_role ?? '';
        if (!in_array($role, ['admin', 'superadmin'], true)) {
            abort(403);
        }
    }

    private function isSuperAdmin(): bool
    {
        return (Auth::user()->user_role === 'superadmin');
    }

    private function adminProvince(): ?string
    {
        return Auth::user()->code_province ?: null;
    }

    #[Computed]
    public function superviseurs(): array
    {
        $q = User::query()
            ->where('is_active', true)
            ->where('user_role', 'superviseur')
            ->orderBy('name');

        if (!$this->isSuperAdmin()) {
            $q->where('code_province', $this->adminProvince());
        }

        return $q->get(['id', 'name', 'email', 'code_province'])
            ->map(fn($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'code_province' => $u->code_province,
            ])->toArray();
    }

    public function updatedSuperviseurId(): void
    {
        $this->dispatchTrendUpdate();
    }

    public function updatedRangeStart(): void
    {
        $this->dispatchTrendUpdate();
    }

    public function updatedRangeEnd(): void
    {
        $this->dispatchTrendUpdate();
    }

    private function ensureScope(User $sup): void
    {
        if ($this->isSuperAdmin()) return;

        // admin ne peut consulter que les superviseurs de sa province
        if ($sup->code_province !== $this->adminProvince()) {
            abort(403);
        }
    }

    private function dispatchTrendUpdate(): void
    {
        if (!$this->superviseurId) return;

        $sup = User::findOrFail($this->superviseurId);
        $this->ensureScope($sup);

        $trendStart = now()->subWeeks(12)->startOfWeek()->format('Y-m-d 00:00:00');
        $trendEnd = now()->format('Y-m-d 23:59:59');

        $rows = DB::table('audit_logs')
            ->selectRaw("date_trunc('week', created_at) as wk, COUNT(*)::int as total")
            ->where('user_id', $sup->id)
            ->where('user_action', 'incident_validated')
            ->whereBetween('created_at', [$trendStart, $trendEnd])
            ->groupBy('wk')
            ->orderBy('wk')
            ->get();

        $labels = [];
        $values = [];
        foreach ($rows as $r) {
            $labels[] = \Carbon\Carbon::parse($r->wk)->format('d M');
            $values[] = (int) $r->total;
        }

        $this->dispatch('supervisor-performance-updated', trend: ['labels' => $labels, 'values' => $values]);
    }

    public function render()
    {
        $stats = [
            'assigned_total' => 0,
            'validated_total' => 0,
            'pending_total' => 0,
            'validation_rate' => 0, // %
            'notes_total' => 0,
            'refs_total' => 0,
        ];

        $recentNotes = [];
        $recentRefs = [];
        $trend = ['labels' => [], 'values' => []];
        $selectedSup = null;

        if ($this->superviseurId) {
            $sup = User::find($this->superviseurId);
            if ($sup) {
                $selectedSup = $sup;
                $this->ensureScope($sup);

                // Fenêtre dates (3 mois) : on inclut toute la journée de rangeEnd
                $start = $this->rangeStart . ' 00:00:00';
                $end = $this->rangeEnd . ' 23:59:59';

                // 1) Incidents assignés au superviseur sur la période (assigned_at)
                $assignedIncidentIds = Incident::query()
                    ->where('assigned_to', $sup->id)
                    ->whereBetween('assigned_at', [$start, $end])
                    ->pluck('id');

                $assignedTotal = $assignedIncidentIds->count();

                // 2) Incidents validés par lui sur la période (audit_logs)
                $validatedCount = 0;
                if ($assignedTotal > 0) {
                    $validatedCount = DB::table('audit_logs')
                        ->where('user_id', $sup->id)
                        ->where('user_action', 'incident_validated')
                        ->whereBetween('created_at', [$start, $end])
                        ->whereIn('model_id', $assignedIncidentIds->all())
                        ->count();
                }

                // 3) En attente de validation (assignés, statut = En attente)
                $pendingCount = 0;
                if ($assignedTotal > 0) {
                    $pendingCount = Incident::query()
                        ->whereIn('id', $assignedIncidentIds->all())
                        ->where('statut_incident', 'En attente')
                        ->count();
                }

                $rate = $assignedTotal > 0 ? (int) round(($validatedCount / $assignedTotal) * 100) : 0;

                // 4) Notes et référencements effectués par ce superviseur sur la période
                $notesTotal = DB::table('case_notes')
                    ->where('created_by', $sup->id)
                    ->whereBetween('created_at', [$start, $end])
                    ->count();

                $refsTotal = DB::table('referencements')
                    ->where('created_by', $sup->id)
                    ->whereBetween('created_at', [$start, $end])
                    ->count();

                $stats = [
                    'assigned_total' => (int) $assignedTotal,
                    'validated_total' => (int) $validatedCount,
                    'pending_total' => (int) $pendingCount,
                    'validation_rate' => (int) $rate,
                    'notes_total' => (int) $notesTotal,
                    'refs_total' => (int) $refsTotal,
                ];

                // 6) Dernières notes (20) + Derniers référencements (20)
                $recentNotes = DB::table('case_notes')
                    ->leftJoin('incidents', 'case_notes.id_incident', '=', 'incidents.id')
                    ->select([
                        'case_notes.id',
                        'case_notes.created_at',
                        'case_notes.is_confidential',
                        'case_notes.case_note',
                        'incidents.code_incident',
                    ])
                    ->where('case_notes.created_by', $sup->id)
                    ->whereBetween('case_notes.created_at', [$start, $end])
                    ->orderByDesc('case_notes.created_at')
                    ->limit(20)
                    ->get()
                    ->map(fn($n) => [
                        'id' => $n->id,
                        'created_at' => $n->created_at,
                        'is_confidential' => (bool) $n->is_confidential,
                        'case_note' => $n->case_note,
                        'code_incident' => $n->code_incident,
                    ])->toArray();

                $recentRefs = DB::table('referencements')
                    ->leftJoin('incidents', 'referencements.id_incident', '=', 'incidents.id')
                    ->leftJoin('service_providers', 'referencements.provider_id', '=', 'service_providers.id')
                    ->select([
                        'referencements.id',
                        'referencements.created_at',
                        'referencements.code_referencement',
                        'referencements.type_reponse',
                        'referencements.statut_reponse',
                        'incidents.code_incident',
                        'service_providers.provider_name',
                        'service_providers.focalpoint_number',
                    ])
                    ->where('referencements.created_by', $sup->id)
                    ->whereBetween('referencements.created_at', [$start, $end])
                    ->orderByDesc('referencements.created_at')
                    ->limit(20)
                    ->get()
                    ->map(fn($r) => [
                        'id' => $r->id,
                        'created_at' => $r->created_at,
                        'code_referencement' => $r->code_referencement,
                        'type_reponse' => $r->type_reponse,
                        'statut_reponse' => $r->statut_reponse,
                        'code_incident' => $r->code_incident,
                        'provider_name' => $r->provider_name,
                        'focalpoint_number' => $r->focalpoint_number,
                    ])->toArray();

                $trendStart = now()->subWeeks(12)->startOfWeek()->format('Y-m-d 00:00:00');
                $trendEnd = now()->format('Y-m-d 23:59:59');

                $rows = DB::table('audit_logs')
                    ->selectRaw("date_trunc('week', created_at) as wk, COUNT(*)::int as total")
                    ->where('user_id', $sup->id)
                    ->where('user_action', 'incident_validated')
                    ->whereBetween('created_at', [$trendStart, $trendEnd])
                    ->groupBy('wk')
                    ->orderBy('wk')
                    ->get();

                $labels = [];
                $values = [];
                foreach ($rows as $r) {
                    $labels[] = \Carbon\Carbon::parse($r->wk)->format('d M');
                    $values[] = (int) $r->total;
                }

                $trend = ['labels' => $labels, 'values' => $values];
            }
        }

        return view('livewire.pages.superviseurs.performance', [
            'selectedSup' => $selectedSup,
            'stats' => $stats,
            'recentNotes' => $recentNotes,
            'recentRefs' => $recentRefs,
            'trend' => $trend,
        ]);
    }
}
