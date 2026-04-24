<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

use App\Models\Incident;
use Livewire\Attributes\Validate;
use Livewire\Form;

class IncidentForm extends Form
{
    #[Validate('nullable|uuid|exists:survivants,id')]
    public ?string $survivant_id = null;

    #[Validate('required|date|before_or_equal:today')]
    public ?string $date_incident = null;

    #[Validate('required|in:Faible,Elevée,Critique')]
    public string $severite = 'Faible';

    #[Validate('required|in:En attente,Validé,Cloturée,Archivé')]
    public string $statut_incident = 'En attente';

    #[Validate('nullable|string|max:255')]
    public ?string $auteur_presume = '';

    #[Validate('required|string|exists:provinces,code_province')]
    public string $code_province = '';

    #[Validate('nullable|string|exists:territoires,code_territoire')]
    public ?string $code_territoire = '';

    #[Validate('nullable|string|exists:zonesantes,code_zonesante')]
    public ?string $code_zonesante = '';

    #[Validate('nullable|string|max:255')]
    public ?string $localite = '';

    #[Validate('nullable|string|max:255')]
    public ?string $source_info = '';

    #[Validate('nullable|string')]
    public ?string $description_faits = '';

    #[Validate('required|in:Standard,Protegé,Confidentielle')]
    public string $confidentiality_level = 'Standard';

    #[Validate('nullable|string|exists:chefferies,code_chefferie')]
    public ?string $code_chefferie = null;

    #[Validate('nullable|string|exists:groupements,code_groupement')]
    public ?string $code_groupement = null;

    #[Validate('nullable|string|exists:airesantes,code_airesante')]
    public ?string $code_airesante = null;

    #[Validate('nullable|string|exists:evenements,code_evenement')]
    public ?string $code_evenement = null;

    public function setIncident(?Incident $incident): void
    {
        if ($incident) {
            $this->survivant_id = $incident->survivant_id;
            $this->date_incident = $incident->date_incident ? $incident->date_incident->toDateString() : null;
            $this->severite = $incident->severite ?? 'Faible';
            $this->statut_incident = $incident->statut_incident ?? 'En attente';
            $this->auteur_presume = $incident->auteur_presume ?? '';
            $this->code_province = $incident->code_province ?? '';
            $this->code_territoire = $incident->code_territoire ?? '';
            $this->code_zonesante = $incident->code_zonesante ?? '';
            $this->localite = $incident->localite ?? '';
            $this->source_info = $incident->source_info ?? '';
            $this->description_faits = $incident->description_faits ?? '';
            $this->confidentiality_level = $incident->confidentiality_level ?? 'Standard';
            $this->code_chefferie = $incident->code_chefferie;
            $this->code_groupement = $incident->code_groupement;
            $this->code_airesante = $incident->code_airesante;
            $this->code_evenement = $incident->code_evenement;
        } else {
            $this->reset();
        }
    }
}
