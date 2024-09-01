<?php

namespace App\Livewire\Notes;

use App\Models\NoteFolder;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ChatSideBar extends Component
{
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
}
