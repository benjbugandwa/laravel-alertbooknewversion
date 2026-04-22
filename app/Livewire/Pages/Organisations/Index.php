<?php

namespace App\Livewire\Pages\Organisations;

use App\Models\Organisation;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $q = '';
    public int $perPage = 10;

    public bool $showModal = false;
    public bool $editing = false;
    public ?int $editingId = null;

    public array $secteurOptions = [
        'Protection',
        'Santé',
        'Education',
        'Sécurité Alimentaire et Nutrition',
        'Eau, Assainissement et Hygiène (WASH)',
        'Abris',
        'Logistique',
    ];

    public array $categorieOptions = [
        'Agence des Nations Unies',
        'ONG Internationale',
        'ONG Nationale',
        'Entité gouvernementale',
        'Autre',
    ];

    public array $form = [
        'org_sigle' => '',
        'org_name' => '',
        'org_secteur_activite' => [], // multi
        'org_categorie' => '',
    ];

    public function updatingQ(): void
    {
        $this->resetPage();
    }
    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    private function isAllowed(): bool
    {
        return in_array(Auth::user()->user_role, ['admin', 'superadmin'], true);
    }

    private function rules(bool $editing = false): array
    {
        return [
            'form.org_sigle' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('organisations', 'org_sigle')->ignore($this->editingId),
            ],
            'form.org_name' => ['required', 'string', 'max:255'],
            'form.org_secteur_activite' => ['nullable', 'array'],
            'form.org_secteur_activite.*' => ['string', Rule::in($this->secteurOptions)],
            'form.org_categorie' => ['nullable', 'string', Rule::in($this->categorieOptions)],
        ];
    }

    private function audit(string $action, string $modelType, string $modelId, array $meta = []): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'user_action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId, // uuid ou string selon ton patch audit_logs
            'ip_address' => request()->ip(),
            'action_meta' => json_encode($meta, JSON_UNESCAPED_UNICODE),
        ]);
    }

    public function openCreate(): void
    {
        if (!$this->isAllowed()) {
            $this->dispatch('toast', message: "Accès refusé.", type: 'error', duration: 5000);
            return;
        }

        $this->resetValidation();
        $this->editing = false;
        $this->editingId = null;

        $this->form = [
            'org_sigle' => '',
            'org_name' => '',
            'org_secteur_activite' => [],
            'org_categorie' => '',
        ];

        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        if (!$this->isAllowed()) {
            $this->dispatch('toast', message: "Accès refusé.", type: 'error', duration: 5000);
            return;
        }

        $this->resetValidation();

        $org = Organisation::findOrFail($id);

        $this->editing = true;
        $this->editingId = $org->id;

        $this->form = [
            'org_sigle' => $org->org_sigle ?? '',
            'org_name' => $org->org_name ?? '',
            'org_secteur_activite' => $org->org_secteur_activite ?? [],
            'org_categorie' => $org->org_categorie ?? '',
        ];

        $this->showModal = true;
    }

    public function save(): void
    {
        if (!$this->isAllowed()) {
            $this->dispatch('toast', message: "Accès refusé.", type: 'error', duration: 5000);
            return;
        }

        $this->validate($this->rules($this->editing));

        // normalisation
        $sigle = trim((string)($this->form['org_sigle'] ?? ''));
        $this->form['org_sigle'] = $sigle !== '' ? mb_strtoupper($sigle) : null;

        $payload = [
            'org_sigle' => $this->form['org_sigle'],
            'org_name' => trim($this->form['org_name']),
            'org_secteur_activite' => array_values(array_unique($this->form['org_secteur_activite'] ?? [])),
            'org_categorie' => $this->form['org_categorie'] ?: null,
        ];

        if ($this->editing && $this->editingId) {
            $org = Organisation::findOrFail($this->editingId);
            $org->update($payload);

            /* $this->audit('organisation_updated', 'organisation', (string)$org->id, [
                'org_name' => $org->org_name,
            ]);*/

            $this->dispatch('toast', message: "Organisation mise à jour.", type: 'success', duration: 5000);
        } else {
            $org = Organisation::create($payload);

            /* $this->audit('organisation_created', 'organisation', (string)$org->id, [
                'org_name' => $org->org_name,
            ]);*/

            $this->dispatch('toast', message: "Organisation créée.", type: 'success', duration: 5000);
        }

        $this->showModal = false;
    }

    public function render()
    {
        $query = Organisation::query();

        if (trim($this->q) !== '') {
            $s = '%' . trim($this->q) . '%';
            $query->where(function ($qq) use ($s) {
                $qq->where('org_name', 'ilike', $s)
                    ->orWhere('org_sigle', 'ilike', $s)
                    ->orWhere('org_categorie', 'ilike', $s);
            });
        }

        $organisations = $query
            ->orderBy('org_name')
            ->paginate($this->perPage);

        return view('livewire.pages.organisations.index', [
            'organisations' => $organisations,
        ]);
    }
}
