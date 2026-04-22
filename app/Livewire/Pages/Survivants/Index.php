<?php

namespace App\Livewire\Pages\Survivants;

use App\Models\Survivant;
use Illuminate\Support\Facades\DB;
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
    public ?string $editingId = null;

    // Form
    public array $form = [
        'full_name' => '',
        'age_survivant' => null,
        'sexe_survivant' => '',
        'marital_status' => '',
        'disability_status' => false,
        'observations' => '',

        // Ajouts
        'adresses' => '',
        'est_mineure' => false,
        'tuteur_nom' => '',
        'tuteur_numero' => '',
    ];

    public function updatingQ(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->editing = false;
        $this->editingId = null;

        $this->form = [
            'full_name' => '',
            'age_survivant' => null,
            'sexe_survivant' => '',
            'marital_status' => '',
            'disability_status' => false,
            'observations' => '',
            'adresses' => '',
            'est_mineure' => false,
            'tuteur_nom' => '',
            'tuteur_numero' => '',
        ];

        $this->showModal = true;
    }

    public function openEdit(string $id): void
    {
        $this->resetValidation();
        $this->editing = true;
        $this->editingId = $id;

        $s = Survivant::findOrFail($id);

        $this->form = [
            'full_name' => $s->full_name ?? '',
            'age_survivant' => $s->age_survivant,
            'sexe_survivant' => $s->sexe_survivant ?? '',
            'marital_status' => $s->marital_status ?? '',
            'disability_status' => (bool) $s->disability_status,
            'observations' => $s->observations ?? '',
            'adresses' => $s->adresses ?? '',
            'est_mineure' => (bool) ($s->est_mineure ?? false),
            'tuteur_nom' => $s->tuteur_nom ?? '',
            'tuteur_numero' => $s->tuteur_numero ?? '',
        ];

        $this->showModal = true;
    }

    private function rules(): array
    {
        return [
            'form.full_name' => ['required', 'string', 'max:255'],
            'form.age_survivant' => ['nullable', 'integer', 'min:0', 'max:120'],
            'form.sexe_survivant' => ['nullable', 'string', 'max:30'],
            'form.marital_status' => ['nullable', 'string', 'max:50'],
            'form.disability_status' => ['nullable', 'boolean'],
            'form.observations' => ['nullable', 'string'],

            'form.adresses' => ['nullable', 'string'],
            'form.est_mineure' => ['nullable', 'boolean'],

            // Obligatoires si est_mineure=true
            'form.tuteur_nom' => [
                Rule::requiredIf(fn() => (bool)($this->form['est_mineure'] ?? false)),
                'string',
                'max:255',
            ],
            'form.tuteur_numero' => [
                Rule::requiredIf(fn() => (bool)($this->form['est_mineure'] ?? false)),
                'string',
                'max:50',
            ],
        ];
    }

    /**
     * Génère code_survivant: SRV-00000
     */
    private function nextCodeSurvivant(): string
    {
        // Récupère le dernier code du type SRV-00001
        $last = DB::table('survivants')
            ->where('code_survivant', 'like', 'SRV-%')
            ->orderByDesc('code_survivant')
            ->value('code_survivant');

        $n = 0;
        if (is_string($last) && preg_match('/^SRV-(\d{5})$/', $last, $m)) {
            $n = (int) $m[1];
        }

        $next = $n + 1;

        return 'SRV-' . str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }

    public function save(): void
    {
        $this->validate($this->rules());

        // Si mineure=false => on vide tuteur
        if (!($this->form['est_mineure'] ?? false)) {
            $this->form['tuteur_nom'] = '';
            $this->form['tuteur_numero'] = '';
        }

        if ($this->editing && $this->editingId) {
            $s = Survivant::findOrFail($this->editingId);
            $s->fill($this->form);
            $s->save();

            $this->dispatch('toast', message: 'Survivant modifié avec succès.', type: 'success', duration: 5000);
        } else {
            // Génération code_survivant
            // (si collision rare: on retente)
            $tries = 0;
            do {
                $code = $this->nextCodeSurvivant();
                $exists = DB::table('survivants')->where('code_survivant', $code)->exists();
                $tries++;
            } while ($exists && $tries < 5);

            $s = new Survivant();
            $s->code_survivant = $code; // champ SQL existant :contentReference[oaicite:9]{index=9}
            $s->fill($this->form);
            $s->save();

            $this->dispatch('toast', message: "Survivant créé : {$code}", type: 'success', duration: 5000);
        }

        $this->showModal = false;
    }

    public function render()
    {
        $query = Survivant::query();

        if (trim($this->q) !== '') {
            $s = '%' . trim($this->q) . '%';
            $query->where(function ($qq) use ($s) {
                $qq->where('code_survivant', 'ilike', $s)
                    ->orWhere('full_name', 'ilike', $s);
            });
        }

        return view('livewire.pages.survivants.index', [
            'survivants' => $query->orderByDesc('code_survivant')->paginate($this->perPage),
        ]);
    }

    public function updatedFormAgeSurvivant($value): void
    {
        $age = is_numeric($value) ? (int) $value : null;

        if ($age !== null && $age < 18) {
            $this->form['est_mineure'] = true;
        } else {
            $this->form['est_mineure'] = false;

            // On vide les champs tuteur si pas mineure
            $this->form['tuteur_nom'] = '';
            $this->form['tuteur_numero'] = '';
        }
    }
}
