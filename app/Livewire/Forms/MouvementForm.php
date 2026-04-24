<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use Livewire\Attributes\Validate;
use App\Models\Mouvement;

class MouvementForm extends Form
{
    public ?Mouvement $mouvement = null;

    public string $date_mouvement = '';
    public string $type_mouvement = '';
    public string $source_info = '';
    
    // Provenance
    public string $code_province_prov = '';
    public string $code_territoire_prov = '';
    public ?string $code_zonesante_prov = null;
    public string $localite_prov = '';

    // Accueil
    public string $code_province_accl = '';
    public string $code_territoire_accl = '';
    public ?string $code_zonesante_accl = null;
    public string $localite_accl = '';

    public ?string $type_logement = null;

    public $estim_nbre_menages = null;
    public $estim_nbre_personnes = null;

    public ?string $remarques_mouvement = null;

    public function rules()
    {
        return [
            'date_mouvement' => ['required', 'date', 'before_or_equal:today'],
            'type_mouvement' => ['required', 'in:Fuite,Retour'],
            'source_info' => ['required', 'string', 'max:255'],
            
            'code_province_prov' => ['required', 'string'],
            'code_territoire_prov' => ['required', 'string'],
            'code_zonesante_prov' => ['nullable', 'string'],
            'localite_prov' => ['required', 'string', 'different:localite_accl'],
            
            'code_province_accl' => ['required', 'string'],
            'code_territoire_accl' => ['required', 'string'],
            'code_zonesante_accl' => ['nullable', 'string'],
            'localite_accl' => ['required', 'string', 'different:localite_prov'],
            
            'type_logement' => ['nullable', 'in:Site spontané,Centre collectif,Famille accueil,Autre'],
            
            'estim_nbre_menages' => ['required', 'integer', 'min:1'],
            'estim_nbre_personnes' => ['required', 'integer', 'min:1', 'gte:estim_nbre_menages'],
            
            'remarques_mouvement' => ['nullable', 'string'],
        ];
    }

    public function messages()
    {
        return [
            'date_mouvement.before_or_equal' => 'La date du mouvement ne peut pas être dans le futur.',
            'localite_prov.different' => 'La localité de provenance doit être différente de la localité d\'accueil.',
            'localite_accl.different' => 'La localité d\'accueil doit être différente de la localité de provenance.',
            'estim_nbre_menages.min' => 'Le nombre de ménages doit être au moins de 1.',
            'estim_nbre_personnes.min' => 'Le nombre de personnes doit être au moins de 1.',
            'estim_nbre_personnes.gte' => 'Le nombre de personnes ne peut pas être inférieur au nombre de ménages.',
        ];
    }

    public function setMouvement(Mouvement $mouvement)
    {
        $this->mouvement = $mouvement;
        $this->date_mouvement = $mouvement->date_mouvement->format('Y-m-d');
        $this->type_mouvement = $mouvement->type_mouvement;
        $this->source_info = $mouvement->source_info;
        
        $this->code_province_prov = $mouvement->code_province_prov;
        $this->code_territoire_prov = $mouvement->code_territoire_prov;
        $this->code_zonesante_prov = $mouvement->code_zonesante_prov;
        $this->localite_prov = $mouvement->localite_prov;

        $this->code_province_accl = $mouvement->code_province_accl;
        $this->code_territoire_accl = $mouvement->code_territoire_accl;
        $this->code_zonesante_accl = $mouvement->code_zonesante_accl;
        $this->localite_accl = $mouvement->localite_accl;

        $this->type_logement = $mouvement->type_logement;
        $this->estim_nbre_menages = $mouvement->estim_nbre_menages;
        $this->estim_nbre_personnes = $mouvement->estim_nbre_personnes;
        $this->remarques_mouvement = $mouvement->remarques_mouvement;
    }
}
