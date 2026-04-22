<?php

namespace App\Livewire\Components;

use App\Models\Province;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class IncidentsExportModal extends Component
{
    public bool $show = false;

    public string $from = '';
    public string $to = '';
    public string $format = 'xlsx'; // xlsx | csv

    // superadmin only
    public ?string $province = null;

    // options
    public bool $include_notes = true;
    public bool $include_referencements = true;
    public bool $include_violences = true;


    protected $listeners = [
        'openIncidentsExport' => 'open',
    ];

    public function mount(): void
    {
        $this->to = now()->toDateString();
        $this->from = now()->subDays(30)->toDateString();
    }

    #[Computed]
    public function provinces(): array
    {
        if (Auth::user()?->user_role === 'superadmin') {
            return Province::query()
                ->select(['code_province', 'nom_province'])
                ->orderBy('nom_province')
                ->get()
                ->map(fn($p) => ['code' => $p->code_province, 'name' => $p->nom_province])
                ->all();
        }
        return [];
    }

    public function open(): void
    {
        $this->resetErrorBag();
        $this->show = true;
    }

    public function close(): void
    {
        $this->show = false;
    }

    public function export()
    {
        $user = Auth::user();

        $data = $this->validate([
            'from' => ['required', 'date'],
            'to' => ['required', 'date', 'after_or_equal:from'],
            'format' => ['required', 'in:csv,xlsx'],
            'province' => ['nullable', 'string'],

            'include_notes' => ['boolean'],
            'include_referencements' => ['boolean'],
            'include_violences' => ['boolean'],
        ]);

        if ($user->user_role !== 'superadmin') {
            $data['province'] = $user->code_province;
        }

        // CSV ne supporte pas multi-feuilles: si user coche referencements, on force xlsx
        if ($data['format'] === 'csv' && ($data['include_referencements'] ?? false)) {
            // on peut aussi afficher un toast, mais simple: forcer xlsx
            $data['format'] = 'xlsx';
        }

        $query = [
            'from' => $data['from'],
            'to' => $data['to'],
            'format' => $data['format'],
            'include_notes' => (int) ($data['include_notes'] ?? false),
            'include_referencements' => (int) ($data['include_referencements'] ?? false),
            'include_violences' => (int) ($data['include_violences'] ?? false),
        ];

        if (!empty($data['province'])) {
            $query['province'] = $data['province'];
        }

        $this->show = false;

        return $this->redirectRoute('exports.incidents', $query, navigate: false);
    }

    public function render()
    {
        return view('livewire.components.incidents-export-modal');
    }
}
