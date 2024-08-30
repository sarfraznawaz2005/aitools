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

    public function addCustomNote(): void
    {
        $this->dispatch('showModal', ['id' => 'addCustomNoteModal']);
    }

    public function deleteNote(Note $note): void
    {
        $note->delete();

        $this->success('Note deleted successfully.');

        $this->dispatch('notesUpdated');
    }
}
