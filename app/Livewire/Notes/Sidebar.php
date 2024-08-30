<?php

namespace App\Livewire\Notes;

use App\Models\NoteFolder;
use App\Traits\InteractsWithToast;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Sidebar extends Component
{
    use InteractsWithToast;

    public NoteFolder $folder;

    public string $name = '';
    public string $color = 'text-gray-600';

    public function mount(NoteFolder $folder = null): void
    {
        $this->folder = $folder ?? new NoteFolder();
    }

    #[Computed]
    public function folders(): Collection
    {
        return NoteFolder::query()->with('notes')->orderBy('name')->get();
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

        $this->folder = $folder;
        $this->fill($folder->toArray());
    }

    public function saveFolder(): void
    {
        $this->validate([
            'name' => 'required|min:3|max:25|unique:note_folders,name,' . ($this->folder->id ?? 'NULL') . ',id',
            'color' => 'required',
        ]);

        $this->folder->fill([
            'name' => $this->name,
            'color' => $this->color,
        ])->save();

        $this->dispatch('closeModal', ['id' => 'notesFolderModal']);

        $this->success($this->folder->wasRecentlyCreated ? 'Folder added successfully!' : 'Folder saved successfully!');

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

        $this->folder = new NoteFolder();

        $this->fill($this->folder->toArray());
    }
}
