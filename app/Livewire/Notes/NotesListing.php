<?php

namespace App\Livewire\Notes;

use App\Models\NoteFolder;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

class NotesListing extends Component
{
    #[Title('Smart Notes')]
    public NoteFolder $folder;

    public function mount(NoteFolder $folder): void
    {
        $this->folder = $folder;
    }

    #[Computed]
    public function notes(): Collection
    {
        return $this->folder->notes()->latest()->get();
    }
}
