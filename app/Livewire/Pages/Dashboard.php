<?php

namespace App\Livewire\Pages;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class Dashboard extends Component
{
    public int $days = 30; // période pour l’évolution (30 derniers jours)

    public $selectedProvince = '';
    public $selectedTerritoire = '';
    public $provinces = [];
    public $territoires = [];

    public function mount()
    {
        $user = Auth::user();
        $isSuper = method_exists($user, 'isSuperAdmin') ? $user->isSuperAdmin() : ($user->user_role === 'superadmin');
        if ($isSuper) {
            $this->provinces = \App\Models\Province::orderBy('nom_province')->get()->toArray();
        }
    }

    public function updatedSelectedProvince($value)
    {
        if ($value) {
            $this->territoires = DB::table('territoires')->where('code_province', $value)->orderBy('nom_territoire')->get()->toArray();
        } else {
            $this->territoires = [];
        }
        $this->selectedTerritoire = '';
    }

    public function setDays(int $days): void
    {
        $this->days = max(7, min($days, 365));
    }

    public function render()
    {
        $user = Auth::user();
        $provinceName = null;

        // Scope: superadmin voit tout; sinon limité à la province du user
        $isSuper = method_exists($user, 'isSuperAdmin') ? $user->isSuperAdmin() : ($user->user_role === 'superadmin');
        
        $provinceScope = $isSuper ? ($this->selectedProvince ?: null) : $user->code_province;
        $territoireScope = $isSuper ? ($this->selectedTerritoire ?: null) : null;

        if ($provinceScope) {
            $provinceName = \App\Models\Province::where('code_province', $provinceScope)
                ->value('nom_province');
        }

        // --------- KPI Users (Cache 15 min) ----------
        $cacheKeyUsers = "dashboard_users_" . ($provinceScope ?: 'all');
        list($usersActive, $usersPending) = Cache::remember($cacheKeyUsers, now()->addMinutes(15), function () use ($provinceScope) {
            $usersActiveQuery = DB::table('users')->where('is_active', true);
            $usersPendingQuery = DB::table('users')->where('is_active', false);

            if ($provinceScope) {
                $usersActiveQuery->where('code_province', $provinceScope);
                $usersPendingQuery->where('code_province', $provinceScope);
            }

            return [
                (int) $usersActiveQuery->count(),
                (int) $usersPendingQuery->count()
            ];
        });

        // --------- Incidents par province (Cache 15 min) ----------
        $cacheKeyProvince = "dashboard_inc_prov_" . ($provinceScope ?: 'all') . "_terr_" . ($territoireScope ?: 'all');
        $byProvince = Cache::remember($cacheKeyProvince, now()->addMinutes(15), function () use ($provinceScope, $territoireScope) {
            $q = DB::table('incidents')
                ->leftJoin('provinces', function($join) {
                    $join->on('incidents.code_province', '=', 'provinces.code_province')
                         ->where('provinces.is_active', 'YES');
                })
                ->selectRaw("COALESCE(provinces.nom_province, incidents.code_province, 'N/A') as label, COUNT(*)::int as total");
            if ($provinceScope) $q->where('incidents.code_province', $provinceScope);
            if ($territoireScope) $q->where('incidents.code_territoire', $territoireScope);
            return $q->groupBy('label')->orderByDesc('total')->limit(15)->get();
        });

        $byProvinceTotal = (int) $byProvince->sum('total');
        $byProvinceTable = $byProvince->map(function ($row) use ($byProvinceTotal) {
            $pct = $byProvinceTotal > 0 ? round(($row->total / $byProvinceTotal) * 100, 1) : 0;
            return [
                'label' => $row->label,
                'total' => (int) $row->total,
                'pct'   => $pct,
            ];
        })->values();

        // --------- Incidents par statut (Cache 15 min) ----------
        $cacheKeyStatus = "dashboard_inc_stat_" . ($provinceScope ?: 'all') . "_terr_" . ($territoireScope ?: 'all');
        $byStatus = Cache::remember($cacheKeyStatus, now()->addMinutes(15), function () use ($provinceScope, $territoireScope) {
            $q = DB::table('incidents')
                ->selectRaw("COALESCE(incidents.statut_incident, 'N/A') as label, COUNT(*)::int as total");
            if ($provinceScope) $q->where('incidents.code_province', $provinceScope);
            if ($territoireScope) $q->where('incidents.code_territoire', $territoireScope);
            return $q->groupBy('label')->orderByDesc('total')->get();
        });

        // --------- Incidents par type d'événement (Cache 15 min) ----------
        $cacheKeyEventType = "dashboard_inc_evt_" . ($provinceScope ?: 'all') . "_terr_" . ($territoireScope ?: 'all');
        $byEventType = Cache::remember($cacheKeyEventType, now()->addMinutes(15), function () use ($provinceScope, $territoireScope) {
            $q = DB::table('incidents')
                ->leftJoin('evenements', 'incidents.code_evenement', '=', 'evenements.code_evenement')
                ->selectRaw("COALESCE(evenements.nom_evenement, incidents.code_evenement, 'N/A') as label, COUNT(*)::int as total");
            if ($provinceScope) $q->where('incidents.code_province', $provinceScope);
            if ($territoireScope) $q->where('incidents.code_territoire', $territoireScope);
            return $q->groupBy('label')->orderByDesc('total')->limit(15)->get();
        });

        // --------- Evolution incidents (X jours) (Cache 15 min) ----------
        $cacheKeyEvo = "dashboard_inc_evo_" . ($provinceScope ?: 'all') . "_terr_" . ($territoireScope ?: 'all') . "_days_" . $this->days;
        $evolution = Cache::remember($cacheKeyEvo, now()->addMinutes(15), function () use ($provinceScope, $territoireScope) {
            $q = DB::table('incidents')
                ->whereNotNull('incidents.date_incident')
                ->where('incidents.date_incident', '>=', now()->subDays($this->days)->startOfDay())
                ->selectRaw("to_char(incidents.date_incident::date, 'YYYY-MM-DD') as d, COUNT(*)::int as total");
            if ($provinceScope) $q->where('incidents.code_province', $provinceScope);
            if ($territoireScope) $q->where('incidents.code_territoire', $territoireScope);
            return $q->groupBy('d')->orderBy('d')->get();
        });

        // --------- Incidents par territoire pour la carte (Cache 15 min) ----------
        $cacheKeyTerritoire = "dashboard_inc_terr_" . ($provinceScope ?: 'all') . "_terr_" . ($territoireScope ?: 'all');
        $byTerritoire = Cache::remember($cacheKeyTerritoire, now()->addMinutes(15), function () use ($provinceScope, $territoireScope) {
            $q = DB::table('incidents')
                ->leftJoin('territoires', 'incidents.code_territoire', '=', 'territoires.code_territoire')
                ->selectRaw("territoires.nom_territoire as label, COUNT(*)::int as total")
                ->whereNotNull('territoires.nom_territoire');
            if ($provinceScope) $q->where('incidents.code_province', $provinceScope);
            if ($territoireScope) $q->where('incidents.code_territoire', $territoireScope);
            return $q->groupBy('label')->get();
        });

        // Préparer datasets pour Chart.js
        $chart = [
            'users' => [
                'active' => $usersActive,
                'pending' => $usersPending,
            ],
            'byProvince' => [
                'labels' => $byProvince->pluck('label')->values(),
                'data' => $byProvince->pluck('total')->values(),
                'table' => $byProvinceTable,
                'sum'   => $byProvinceTotal,
            ],
            'byStatus' => [
                'labels' => $byStatus->pluck('label')->values(),
                'data' => $byStatus->pluck('total')->values(),
            ],
            'byEventType' => [
                'labels' => $byEventType->pluck('label')->values(),
                'data' => $byEventType->pluck('total')->values(),
            ],
            'evolution' => [
                'labels' => $evolution->pluck('d')->values(),
                'data' => $evolution->pluck('total')->values(),
            ],
            'byTerritoire' => $byTerritoire->mapWithKeys(function ($item) {
                return [strtolower(trim($item->label)) => $item->total];
            })->toArray(),
            'scope' => [
                'isSuper' => $isSuper,
                'code_province' => $provinceScope,
                'nom_province' => $provinceName,
            ],
        ];

        return view('livewire.pages.dashboard', [
            'chart' => $chart,
        ]);
    }
}
