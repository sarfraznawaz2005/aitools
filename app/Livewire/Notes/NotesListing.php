<?php

namespace App\Livewire\Notes;

use App\Models\Note;
use App\Models\NoteFolder;
use App\Traits\InteractsWithToast;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class NotesListing extends Component
{
    use InteractsWithToast;
    use WithPagination;

    #[Title('Smart Notes')]
    public NoteFolder $folder;

    protected $listeners = [
        'folderUpdated' => '$refresh',
        'notesUpdated' => '$refresh',
    ];

    public string $searchQuery = '';

    public string $sortField = 'id';
    public bool $sortAsc = false;

    public function mount(NoteFolder $folder): void
    {
        //openWindow('note', 'note-window', ['id' => 1]);

        $this->folder = $folder;
    }

    #[Computed]
    public function folders(): Collection
    {
        return NoteFolder::query()->with('notes')->orderBy('name')->get();
    }

    #[Computed]
    public function notes(): LengthAwarePaginator
    {
        return $this->folder->notes()
            ->where(function ($query) {
                $query->where('content', 'like', '%' . $this->searchQuery . '%')
                    ->orWhere('title', 'like', '%' . $this->searchQuery . '%');
            })
            ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
            ->paginate(10);

    }

    public function sortBy($field): void
    {
        if ($this->sortField === $field) {
            $this->sortAsc = !$this->sortAsc;
        } else {
            $this->sortAsc = true;
        }

        $this->sortField = $field;
    }

    #[On('folderDeleted')]
    public function folderDeleted(NoteFolder $folder): void
    {
        if ($folder->id === $this->folder->id) {
            $this->redirect(route('smart-notes'), true);
        }
    }

    public function moveToFolder(NoteFolder $folder, Note $note): void
    {
        $note->update(['note_folder_id' => $folder->id]);

        $this->dispatch('notesUpdated');

        $this->success('Note moved successfully!');
    }


    public function deleteNote(Note $note): void
    {
        if ($note->delete()) {
            // doing redirect because otherwise was getting strange livewire $headers error
            $this->redirect(route('smart-notes.listing', $this->folder->id), true);
        }
    }
}
