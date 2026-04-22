<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

use App\Models\CaseNote;
use Livewire\Attributes\Validate;
use Livewire\Form;

class CaseNoteForm extends Form
{
    #[Validate('required|string|min:3')]
    public string $case_note = '';

    #[Validate('boolean')]
    public bool $is_confidential = false;

    public function setCaseNote(?CaseNote $note): void
    {
        if ($note) {
            $this->case_note = $note->case_note ?? '';
            $this->is_confidential = (bool) $note->is_confidential;
        } else {
            $this->reset();
        }
    }
}
