<?php

namespace App\Livewire\Pages\Documents;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Document;
use Illuminate\Support\Facades\Storage;
use App\Livewire\Forms\DocumentForm;

class Index extends Component
{
    use WithPagination, WithFileUploads;

    public DocumentForm $form;
    
    public $file; // for upload
    public string $q = '';
    public string $f_category = '';
    
    public bool $showModal = false;
    public bool $editing = false;
    public ?string $editingId = null;

    public function categories()
    {
        return ['Rapport', 'Note', 'Evaluation', 'Dashboard', 'Carte', 'Autre'];
    }

    public function canEditOrAdd()
    {
        return in_array(auth()->user()->user_role, ['superadmin', 'admin', 'superviseur']);
    }

    public function updatingQ()
    {
        $this->resetPage();
    }

    public function updatingFCategory()
    {
        $this->resetPage();
    }

    public function openCreate()
    {
        if (!$this->canEditOrAdd()) {
            $this->dispatch('toast', message: 'Action non autorisée', type: 'error');
            return;
        }

        $this->resetValidation();
        $this->form->resetForm();
        $this->file = null;
        $this->editing = false;
        $this->editingId = null;
        $this->showModal = true;
    }

    public function openEdit($id)
    {
        if (!$this->canEditOrAdd()) {
            $this->dispatch('toast', message: 'Action non autorisée', type: 'error');
            return;
        }

        $document = Document::findOrFail($id);

        if ($document->uploaded_by !== auth()->id()) {
            $this->dispatch('toast', message: 'Seul le créateur peut modifier ce document.', type: 'error');
            return;
        }

        $this->resetValidation();
        $this->form->setDocument($document);
        $this->file = null;
        $this->editing = true;
        $this->editingId = $id;
        $this->showModal = true;
    }

    public function save()
    {
        if (!$this->canEditOrAdd()) {
            return;
        }

        $this->form->validate();

        if (!$this->editing) {
            $this->validate([
                'file' => 'required|file|max:20480', // 20MB max
            ]);
        } else {
            $this->validate([
                'file' => 'nullable|file|max:20480',
            ]);
        }

        if ($this->editing) {
            $document = Document::findOrFail($this->editingId);
            if ($document->uploaded_by !== auth()->id()) {
                $this->dispatch('toast', message: 'Seul le créateur peut modifier ce document.', type: 'error');
                return;
            }

            $document->doc_name = $this->form->doc_name;
            $document->doc_summary = $this->form->doc_summary;
            $document->doc_category = $this->form->doc_category;

            if ($this->file) {
                if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                    Storage::disk('public')->delete($document->file_path);
                }
                
                $path = $this->file->store('documents', 'public');
                $document->file_path = $path;
                $document->original_name = $this->file->getClientOriginalName();
                $document->mime_type = $this->file->getMimeType();
                $document->file_type = $this->file->getClientOriginalExtension();
            }

            $document->save();
            $this->dispatch('toast', message: 'Document mis à jour avec succès.', type: 'success');
        } else {
            $path = $this->file->store('documents', 'public');

            Document::create([
                'doc_name' => $this->form->doc_name,
                'doc_summary' => $this->form->doc_summary,
                'doc_category' => $this->form->doc_category,
                'file_path' => $path,
                'mime_type' => $this->file->getMimeType(),
                'original_name' => $this->file->getClientOriginalName(),
                'file_type' => $this->file->getClientOriginalExtension(),
                'uploaded_by' => auth()->id(),
                'download_count' => 0,
            ]);
            $this->dispatch('toast', message: 'Document ajouté avec succès.', type: 'success');
        }

        $this->showModal = false;
    }

    public function download($id)
    {
        $document = Document::findOrFail($id);
        
        $document->increment('download_count');

        if (!Storage::disk('public')->exists($document->file_path)) {
            $this->dispatch('toast', message: 'Fichier introuvable sur le serveur.', type: 'error');
            return;
        }

        return Storage::disk('public')->download($document->file_path, $document->original_name);
    }
    
    public function shareWhatsapp($id)
    {
        $document = Document::findOrFail($id);
        
        $url = asset('storage/' . $document->file_path);
        $text = "Document: " . $document->doc_name . "\nCatégorie: " . $document->doc_category . "\nLien: " . $url;
        
        $waUrl = 'https://wa.me/?text=' . urlencode($text);
        $this->dispatch('open-url', url: $waUrl);
    }

    public function render()
    {
        $query = Document::query()->with('uploader');

        if ($this->q !== '') {
            $query->where(function($q) {
                $q->where('doc_name', 'like', '%' . $this->q . '%')
                  ->orWhere('original_name', 'like', '%' . $this->q . '%')
                  ->orWhere('doc_summary', 'like', '%' . $this->q . '%');
            });
        }

        if ($this->f_category !== '') {
            $query->where('doc_category', $this->f_category);
        }

        $documents = $query->orderByDesc('created_at')->paginate(10);

        return view('livewire.pages.documents.index', [
            'documents' => $documents,
            'categories' => $this->categories(),
        ]);
    }
}
