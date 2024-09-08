<?php

namespace App\Livewire\Notes;

use App\Models\Note;
use App\Models\NoteFolder;
use App\Traits\InteractsWithToast;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Native\Laravel\Facades\Window;

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

    public bool $loaded = false;
    public Collection $folders;

    public function load(): void
    {
        $this->folders = NoteFolder::query()->with('notes')->orderBy('name')->get();

        $this->loaded = true;
    }

    public function placeholder(): string
    {
        return '
            <div class="flex justify-center items-center h-full w-full z-[1000]">
                <svg class="animate-spin h-12 w-12 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
            </div>
    ';
    }

    public function mount(NoteFolder $folder): void
    {
        //openWindow('note', 'note-window', ['id' => 1]);

        $this->folder = $folder;
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

    public function viewNote(Note $note): void
    {
        try {
            Window::close('viewNoteWindow');
        } catch (Exception) {
        } finally {
            openWindow(
                'viewNoteWindow', 'view-note-window', ['id' => $note->id],
                true, true, true, true, 1024, 700
            );
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
