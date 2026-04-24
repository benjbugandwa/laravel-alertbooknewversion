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

class Index extends Component
{
    use WithPagination;

    public Incident $incident;
    public MouvementForm $form;
    
    // Filters
    public string $f_type = '';
    public string $f_zone_prov = '';
    public string $f_zone_accl = '';
    
    public bool $showModal = false;
    public bool $editing = false;
    public ?int $editingId = null;

    // Reference Data
    public array $provinces = [];
    public array $territoires_prov = [];
    public array $zones_prov = [];
    
    public array $territoires_accl = [];
    public array $zones_accl = [];

    public function mount(Incident $incident)
    {
        $this->incident = $incident;
        $this->provinces = Province::orderBy('nom_province')->get()->toArray();
        $this->updateTerritoiresProv();
        $this->updateTerritoiresAccl();
    }

    public function canAddMouvement()
    {
        // moniteur, superviseur, admin, superadmin can add
        return in_array(auth()->user()->user_role, ['superadmin', 'admin', 'superviseur', 'moniteur']);
    }

    public function canEditMouvement()
    {
        // superviseur, admin, superadmin can edit
        return in_array(auth()->user()->user_role, ['superadmin', 'admin', 'superviseur']);
    }

    public function updating($field)
    {
        if (in_array($field, ['f_type', 'f_zone_prov', 'f_zone_accl'])) {
            $this->resetPage();
        }
    }

    // Dynamic dropdown updates for Provenance
    public function updateTerritoiresProv()
    {
        // For non-superadmin, it will always be incident's province
        $code_province = $this->form->code_province_prov ?? $this->incident->code_province;
        
        if ($code_province) {
            $this->territoires_prov = Territoire::where('code_province', $code_province)->orderBy('nom_territoire')->get()->toArray();
        } else {
            $this->territoires_prov = [];
        }
        $this->updateZonesProv();
    }

    public function updatedFormCodeProvinceProv()
    {
        if (auth()->user()->user_role !== 'superadmin') {
            $this->form->code_province_prov = $this->incident->code_province;
            return;
        }
        $this->updateTerritoiresProv();
        $this->form->code_territoire_prov = '';
        $this->form->code_zonesante_prov = '';
    }

    public function updatedFormCodeTerritoireProv()
    {
        if (auth()->user()->user_role !== 'superadmin') {
            $this->form->code_territoire_prov = $this->incident->code_territoire;
            return;
        }
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
        if (!$this->canAddMouvement()) {
            $this->dispatch('toast', message: 'Action non autorisée', type: 'error');
            return;
        }

        $this->resetValidation();
        $this->form->reset();
        
        // Initialiser la provenance par défaut (celle de l'incident)
        $this->form->code_province_prov = $this->incident->code_province;
        $this->form->code_territoire_prov = $this->incident->code_territoire;
        
        $this->form->code_province_accl = '';
        
        $this->editing = false;
        $this->editingId = null;
        $this->showModal = true;
        
        $this->updateTerritoiresProv();
        $this->updateTerritoiresAccl();
    }

    public function openEdit($id)
    {
        if (!$this->canEditMouvement()) {
            $this->dispatch('toast', message: 'Seuls les superviseurs ou administrateurs peuvent modifier un mouvement.', type: 'error');
            return;
        }

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
        if ($this->editing && !$this->canEditMouvement()) {
            return;
        }
        if (!$this->editing && !$this->canAddMouvement()) {
            return;
        }

        $this->form->validate();

        $data = $this->form->all();
        
        // Forcer les valeurs de provenance si l'utilisateur n'est pas superadmin
        if (auth()->user()->user_role !== 'superadmin') {
            $data['code_province_prov'] = $this->incident->code_province;
            $data['code_territoire_prov'] = $this->incident->code_territoire;
        }
        
        if ($this->editing) {
            $mouvement = Mouvement::findOrFail($this->editingId);
            $mouvement->update($data);
            $this->dispatch('toast', message: 'Mouvement mis à jour avec succès.', type: 'success');
        } else {
            $data['incident_id'] = $this->incident->id;
            $data['created_by'] = auth()->id();
            Mouvement::create($data);
            $this->dispatch('toast', message: 'Mouvement ajouté avec succès.', type: 'success');
        }

        $this->showModal = false;
    }

    public function render()
    {
        $query = Mouvement::where('incident_id', $this->incident->id)->with(['creator', 'territoireProv', 'territoireAccl']);

        if ($this->f_type !== '') {
            $query->where('type_mouvement', $this->f_type);
        }

        if ($this->f_zone_prov !== '') {
            $query->where('code_zonesante_prov', $this->f_zone_prov);
        }
        
        if ($this->f_zone_accl !== '') {
            $query->where('code_zonesante_accl', $this->f_zone_accl);
        }

        $mouvements = $query->orderByDesc('created_at')->paginate(10);
        
        // Toutes les zones de santé pour les filtres
        $all_zones = ZoneSante::orderBy('nom_zonesante')->get();

        return view('livewire.pages.mouvements.index', [
            'mouvements' => $mouvements,
            'all_zones' => $all_zones,
        ]);
    }
}
