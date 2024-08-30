<?php

namespace App\Livewire\Notes;

use App\Models\Note;
use App\Models\NoteFolder;
use App\Traits\InteractsWithToast;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

class NotesListing extends Component
{
    use InteractsWithToast;

    #[Title('Smart Notes')]
    public NoteFolder $folder;

    protected $listeners = [
        'folderUpdated' => '$refresh',
        'notesUpdated' => '$refresh',
    ];

    public function mount(NoteFolder $folder): void
    {
        $this->folder = $folder;
    }

    #[Computed]
    public function notes(): Collection
    {
        return $this->folder->notes()->latest()->get();
    }

    #[On('folderDeleted')]
    public function folderDeleted(NoteFolder $folder): void
    {
        if ($folder->id === $this->folder->id) {
            $this->redirect(route('smart-notes'), true);
        }
    }

    public function deleteNote(Note $note): void
    {
        if ($note->delete()) {
            // doing redirect because otherwise was getting strange livewire $headers error
            $this->redirect(route('smart-notes.listing', $this->folder->id), true);
        }
    }
}
