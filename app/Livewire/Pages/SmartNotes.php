<?php

namespace App\Livewire\Pages;

use App\Models\NoteFolder;
use App\Traits\InteractsWithToast;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

class SmartNotes extends Component
{
    use InteractsWithToast;

    #[Title('Smart Notes')]
    public function render(): View|Application|Factory
    {
        return view('livewire.pages.smart-notes');
    }

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

    public function deleteFolder(NoteFolder $folder): void
    {
        $folder->delete();

        $this->success('Folder deleted successfully.');
    }

}
