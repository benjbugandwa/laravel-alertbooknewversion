<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use Livewire\Attributes\Validate;
use App\Models\Document;

class DocumentForm extends Form
{
    public ?Document $document = null;

    #[Validate('required|string|max:255')]
    public string $doc_name = '';

    #[Validate('nullable|string')]
    public ?string $doc_summary = null;

    #[Validate('required|string|in:Rapport,Note,Evaluation,Dashboard,Carte,Autre')]
    public string $doc_category = '';

    public function setDocument(Document $document)
    {
        $this->document = $document;
        $this->doc_name = $document->doc_name;
        $this->doc_summary = $document->doc_summary;
        $this->doc_category = $document->doc_category;
    }

    public function resetForm()
    {
        $this->reset(['doc_name', 'doc_summary', 'doc_category']);
        $this->document = null;
    }
}
