<?php

namespace App\Livewire\Components;

use App\Models\Incident;
use App\Models\Violence;
use App\Models\ViolenceIncident;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\AuditLog;
use Livewire\Attributes\Computed;
use Livewire\Component;

class IncidentViolencesModal extends Component
{
    public bool $open = false;

    public ?string $incidentId = null;

    public string $q = '';

    /** @var array<int,bool>  violenceId => checked */
    public array $selected = [];

    /** @var array<int,string> violenceId => description */
    public array $descriptions = [];

    protected $listeners = ['openIncidentViolences' => 'open'];

    public function open(string $incidentId): void
    {
        $this->resetErrorBag();
        $this->incidentId = $incidentId;
        $this->open = true;

        // Précharger sélection existante
        $existing = ViolenceIncident::query()
            ->where('id_incident', $incidentId)
            ->get(['id_violence', 'description_violence']);

        $this->selected = [];
        $this->descriptions = [];

        foreach ($existing as $row) {
            $this->selected[(int)$row->id_violence] = true;
            $this->descriptions[(int)$row->id_violence] = $row->description_violence ?? '';
        }
    }

    private function audit(string $action, string $modelType, string $modelId, array $meta = []): void
    {
        AuditLog::create([
            'id' => random_int(100000000, 999999999),
            'user_id' => Auth::user()->id,
            'user_action' => $action,
            'model_type' => $modelType,  // ex: 'incident'
            'model_id' => $modelId,      // UUID
            'ip_address' => request()->ip(),
            'action_meta' => json_encode($meta, JSON_UNESCAPED_UNICODE),
        ]);
    }

    #[Computed]
    public function violencesGrouped(): array
    {
        $query = Violence::query();

        if (trim($this->q) !== '') {
            $query->where(function ($q) {
                $q->where('violence_name', 'ilike', '%' . $this->q . '%')
                    ->orWhere('categorie_name', 'ilike', '%' . $this->q . '%');
            });
        }

        $rows = $query
            ->orderBy('categorie_name')
            ->orderBy('violence_name')
            ->get(['id', 'violence_name', 'categorie_name']);

        // Group by categorie_name
        $grouped = [];
        foreach ($rows as $v) {
            $cat = $v->categorie_name ?: 'Autres';
            $grouped[$cat][] = [
                'id' => (int)$v->id,
                'name' => $v->violence_name,
            ];
        }

        return $grouped;
    }

    public function save(): void
    {
        if (!$this->incidentId) return;

        // Règles : incident non archivé/clôturé
        $incident = Incident::findOrFail($this->incidentId);

        if (in_array($incident->statut_incident, ['Cloturée', 'Archivé'], true)) {
            $this->dispatch('toast', message: "Incident clôturé/archivé : modification impossible.", type: 'warning', duration: 6000);
            return;
        }

        // Construire la liste des ids cochés
        $ids = collect($this->selected)
            ->filter(fn($v) => $v === true)
            ->keys()
            ->map(fn($id) => (int)$id)
            ->values()
            ->all();

        // Avant transaction : état actuel en DB
        $beforeIds = ViolenceIncident::query()
            ->where('id_incident', $this->incidentId)
            ->pluck('id_violence')
            ->map(fn($x) => (int)$x)
            ->all();

        // Après : ids cochés
        $afterIds = $ids;

        // Diff
        $added = array_values(array_diff($afterIds, $beforeIds));
        $removed = array_values(array_diff($beforeIds, $afterIds));



        DB::transaction(function () use ($ids) {
            // Supprimer ceux décochés
            ViolenceIncident::query()
                ->where('id_incident', $this->incidentId)
                ->whereNotIn('id_violence', $ids ?: [0])
                ->delete();

            // Audit log


            // Upsert pour ceux cochés
            foreach ($ids as $violenceId) {
                $desc = trim($this->descriptions[$violenceId] ?? '');

                // existe ?
                $existing = ViolenceIncident::query()
                    ->where('id_incident', $this->incidentId)
                    ->where('id_violence', $violenceId)
                    ->first();

                if ($existing) {
                    $existing->description_violence = $desc ?: null;
                    $existing->save();
                } else {
                    ViolenceIncident::create([
                        'id' => random_int(100000000, 999999999),
                        'id_incident' => $this->incidentId,
                        'id_violence' => $violenceId,
                        'description_violence' => $desc ?: null,
                        'created_by' => Auth::id(),
                        'created_at' => now(),
                    ]);
                }
            }
        });

        $this->audit('incident_violences_updated', 'incident', $incident->id, [
            'added' => $added,
            'removed' => $removed,
            'total' => count($afterIds),
        ]);

        $this->open = false;

        $this->dispatch('toast', message: "Types de violences enregistrés.", type: 'success', duration: 5000);

        // Optionnel : informer la page incidents de refresh
        $this->dispatch('violences-updated', incidentId: $this->incidentId);
    }

    public function render()
    {
        return view('livewire.components.incident-violences-modal');
    }
}
