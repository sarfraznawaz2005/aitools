<?php

namespace App\Livewire\Notes;

use App\Models\Note;
use App\Models\NoteFolder;
use App\Traits\InteractsWithToast;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class TextNote extends Component
{
    use InteractsWithToast;

    public Note $note;
    public NoteFolder $folder;

    public string $note_folder_id;
    public string $title = '';
    public string $content = '';

    public function mount(): void
    {
        $this->note_folder_id = $this->folder->id ?? '';
    }

    #[Computed]
    public function folders(): Collection
    {
        return NoteFolder::query()->with('notes')->orderBy('name')->get();
    }

    #[On('openCustomModal')]
    public function openCustomModal(): void
    {
        $this->resetForm();

        $this->dispatch('showModal', ['id' => 'addCustomNoteModal']);
    }

    #[On('openCustomModalForEdit')]
    public function openCustomModalForEdit(Note $note): void
    {
        $this->note = $note;

        $this->fill($note->toArray());

        $this->dispatch('showModal', ['id' => 'addCustomNoteModal']);
    }

    public function saveNote(): void
    {
        $this->validate([
            'note_folder_id' => 'required',
            'title' => 'required|min:4',
            'content' => 'required|min:5',
        ]);

        $this->note->fill([
            'note_folder_id' => $this->note_folder_id,
            'title' => $this->title,
            'content' => $this->content,
        ])->save();


        $this->success($this->note->wasRecentlyCreated ? 'Note added successfully!' : 'Note saved successfully!');

        $this->dispatch('notesUpdated');
        $this->dispatch('closeModal', ['id' => 'addCustomNoteModal']);

        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->reset(['title', 'content']);

        $this->resetErrorBag();

        $this->note = new Note();
        $this->note_folder_id = $this->folder->id ?? '';
    }
}
