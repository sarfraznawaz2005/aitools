<?php

namespace App\Livewire\Pages;

use App\Models\NoteFolder;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

class SmartNotes extends Component
{
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
}
