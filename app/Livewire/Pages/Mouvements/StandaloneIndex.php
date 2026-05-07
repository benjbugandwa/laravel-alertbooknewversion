<?php

namespace App\Livewire\Pages\Mouvements;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Mouvement;
use App\Models\Incident;
use App\Livewire\Forms\MouvementForm;
use App\Models\Province;
use App\Models\Territoire;
use App\Models\ZoneSante;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;

#[Title('Gestion des Déplacements')]
class StandaloneIndex extends Component
{
    use WithPagination;

    public MouvementForm $form;
    
    // Filters
    public string $f_type = '';
    public string $f_province = '';
    public string $f_territoire = '';
    public string $search = '';
    
    public bool $showModal = false;
    public bool $editing = false;
    public ?int $editingId = null;

    // Reference Data
    public array $provinces = [];
    public array $territoires_prov = [];
    public array $zones_prov = [];
    
    public array $territoires_accl = [];
    public array $zones_accl = [];
    
    public array $all_incidents = [];

    // Export Filters
    public bool $showExportModal = false;
    public string $exp_start_date = '';
    public string $exp_end_date = '';
    public string $exp_province = '';
    public string $exp_territoire = '';
    public string $exp_zonesante = '';
    public array $exp_territoires = [];
    public array $exp_zones = [];

    public function mount()
    {
        $user = Auth::user();
        if ($user->user_role === 'moniteur') {
            abort(403);
        }

        $this->provinces = Province::orderBy('nom_province')->get()->toArray();
        
        // Charger les incidents récents pour le lien optionnel
        $this->all_incidents = Incident::orderByDesc('date_incident')
            ->limit(100)
            ->get(['id', 'code_incident', 'localite', 'date_incident'])
            ->toArray();
            
        $this->updateTerritoiresProv();
        $this->updateTerritoiresAccl();
    }

    public function updating($field)
    {
        if (in_array($field, ['f_type', 'f_province', 'f_territoire', 'search'])) {
            $this->resetPage();
        }
    }

    // Dynamic dropdown updates for Provenance
    public function updateTerritoiresProv()
    {
        if ($this->form->code_province_prov) {
            $this->territoires_prov = Territoire::where('code_province', $this->form->code_province_prov)->orderBy('nom_territoire')->get()->toArray();
        } else {
            $this->territoires_prov = [];
        }
        $this->updateZonesProv();
    }

    public function updatedFormCodeProvinceProv()
    {
        $this->updateTerritoiresProv();
        $this->form->code_territoire_prov = '';
        $this->form->code_zonesante_prov = '';
    }

    public function updatedFormCodeTerritoireProv()
    {
        $this->updateZonesProv();
        $this->form->code_zonesante_prov = '';
    }

    public function updateZonesProv()
    {
        if ($this->form->code_territoire_prov) {
            $this->zones_prov = ZoneSante::where('code_territoire', $this->form->code_territoire_prov)->orderBy('nom_zonesante')->get()->toArray();
        } else {
            $this->zones_prov = [];
        }
    }

    // Dynamic dropdown updates for Accueil
    public function updateTerritoiresAccl()
    {
        if ($this->form->code_province_accl) {
            $this->territoires_accl = Territoire::where('code_province', $this->form->code_province_accl)->orderBy('nom_territoire')->get()->toArray();
        } else {
            $this->territoires_accl = [];
        }
        $this->updateZonesAccl();
    }

    public function updatedFormCodeProvinceAccl()
    {
        $this->updateTerritoiresAccl();
        $this->form->code_territoire_accl = '';
        $this->form->code_zonesante_accl = '';
    }

    public function updatedFormCodeTerritoireAccl()
    {
        $this->updateZonesAccl();
        $this->form->code_zonesante_accl = '';
    }

    public function updateZonesAccl()
    {
        if ($this->form->code_territoire_accl) {
            $this->zones_accl = ZoneSante::where('code_territoire', $this->form->code_territoire_accl)->orderBy('nom_zonesante')->get()->toArray();
        } else {
            $this->zones_accl = [];
        }
    }

    public function openCreate()
    {
        $this->resetValidation();
        $this->form->reset();
        
        $this->editing = false;
        $this->editingId = null;
        $this->showModal = true;
        
        $this->updateTerritoiresProv();
        $this->updateTerritoiresAccl();
    }

    public function openEdit($id)
    {
        $mouvement = Mouvement::findOrFail($id);

        $this->resetValidation();
        $this->form->setMouvement($mouvement);
        $this->editing = true;
        $this->editingId = $id;
        
        $this->updateTerritoiresProv();
        $this->updateTerritoiresAccl();
        
        $this->showModal = true;
    }

    public function save()
    {
        $this->form->validate();

        $data = $this->form->getData();
        
        if ($this->editing) {
            $mouvement = Mouvement::findOrFail($this->editingId);
            $mouvement->update($data);
            $this->dispatch('toast', message: 'Mouvement mis à jour avec succès.', type: 'success');
        } else {
            $data['created_by'] = auth()->id();
            Mouvement::create($data);
            $this->dispatch('toast', message: 'Mouvement ajouté avec succès.', type: 'success');
        }

        $this->showModal = false;
    }

    public function delete($id)
    {
        if (Auth::user()->user_role !== 'superadmin') {
            $this->dispatch('toast', message: 'Action réservée aux administrateurs.', type: 'error');
            return;
        }

        Mouvement::destroy($id);
        $this->dispatch('toast', message: 'Mouvement supprimé.', type: 'success');
    }

    // Export Logic
    public function openExport()
    {
        $this->showExportModal = true;
    }

    public function updatedExpProvince()
    {
        $this->exp_territoires = Territoire::where('code_province', $this->exp_province)->orderBy('nom_territoire')->get()->toArray();
        $this->exp_territoire = '';
        $this->exp_zones = [];
        $this->exp_zonesante = '';
    }

    public function updatedExpTerritoire()
    {
        $this->exp_zones = ZoneSante::where('code_territoire', $this->exp_territoire)->orderBy('nom_zonesante')->get()->toArray();
        $this->exp_zonesante = '';
    }

    public function export()
    {
        $filters = [
            'start_date' => $this->exp_start_date,
            'end_date' => $this->exp_end_date,
            'territoire' => $this->exp_territoire,
            'zonesante' => $this->exp_zonesante,
        ];

        $filename = 'export_mouvements_' . now()->format('Ymd_His') . '.xlsx';
        
        $this->showExportModal = false;
        
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\MovementsExport($filters), 
            $filename
        );
    }

    public function render()
    {
        $query = Mouvement::with(['creator', 'territoireProv', 'territoireAccl', 'incident']);

        if ($this->f_type !== '') {
            $query->where('type_mouvement', $this->f_type);
        }

        if ($this->f_province !== '') {
            $query->where('code_province_accl', $this->f_province);
        }
        
        if ($this->search !== '') {
            $query->where(function($q) {
                $q->where('localite_prov', 'like', '%' . $this->search . '%')
                  ->orWhere('localite_accl', 'like', '%' . $this->search . '%')
                  ->orWhere('cause_deplacement', 'like', '%' . $this->search . '%');
            });
        }

        $mouvements = $query->orderByDesc('date_mouvement')->paginate(15);

        return view('livewire.pages.mouvements.standalone-index', [
            'mouvements' => $mouvements,
        ]);
    }
}
