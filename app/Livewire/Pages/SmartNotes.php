<?php

namespace App\Livewire\Pages;

use App\Models\NoteFolder;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

class SmartNotes extends Component
{
    #[Title('Smart Notes')]
    #[Computed]
    public function folders(): Collection
    {
        return NoteFolder::query()->with('notes')->orderBy('name')->get();
    }

    #[Computed]
    public function totalNotesCount(): int
    {
        return NoteFolder::query()->withCount('notes')->get()->sum('notes_count');
    }

    public function addCustomNote(): void
    {
        $this->dispatch('showModal', ['id' => 'addCustomNoteModal']);
    }
}
