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

    public NoteFolder $model;

    public string $name = '';
    public string $color = 'text-gray-600';

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

    public function addFolder(): void
    {
        $this->resetForm();

        $this->dispatch('showModal', ['id' => 'notesFolderModal']);
    }

    public function editFolder(NoteFolder $folder): void
    {
        $this->dispatch('showModal', ['id' => 'notesFolderModal']);

        $this->resetErrorBag();

        $this->model = $folder;
        $this->fill($folder->toArray());
    }

    public function saveFolder(): void
    {
        $this->validate([
            'name' => 'required|min:3|max:25|unique:note_folders,name,' . ($this->model->id ?? 'NULL') . ',id',
            'color' => 'required',
        ]);

        $this->model->fill([
            'name' => $this->name,
            'color' => $this->color,
        ])->save();

        $this->dispatch('closeModal', ['id' => 'notesFolderModal']);

        $this->success($this->model->wasRecentlyCreated ? 'Folder added successfully!' : 'Folder saved successfully!');

        $this->resetForm();
    }

    public function deleteFolder(NoteFolder $folder): void
    {
        $folder->delete();

        $this->success('Folder deleted successfully.');
    }

    public function resetForm(): void
    {
        $this->reset();

        $this->resetErrorBag();

        $this->model = new NoteFolder();

        $this->fill($this->model->toArray());
    }
}
