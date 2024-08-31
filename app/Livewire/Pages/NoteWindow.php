<?php

namespace App\Livewire\Pages;

use App\Models\Note;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Native\Laravel\Facades\Window;

class NoteWindow extends Component
{
    public int $id;

    public function mount(int $id): void
    {
        $this->id = $id;
    }

    #[Layout('components/layouts/headerless')]
    public function render(): View|Application|Factory
    {
        $note = Note::query()->findOrFail($this->id);

        return view('livewire.pages.note-window', compact('note'));
    }

    public function close(): void
    {
        Window::close('note');
    }
}
