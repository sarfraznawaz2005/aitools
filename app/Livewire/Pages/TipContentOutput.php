<?php

namespace App\Livewire\Pages;

use App\Models\TipContent;
use App\Traits\InteractsWithToast;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Native\Laravel\Facades\Window;

class TipContentOutput extends Component
{
    use InteractsWithToast;

    public int $id;
    public TipContent $model;

    public function mount(int $id): void
    {
        $this->id = $id;
    }

    #[Layout('components/layouts/headerless')]
    public function render(): View|Factory|Application
    {
        $tipContent = $this->model = TipContent::query()->findOrFail($this->id);

        return view('livewire.pages.tip-content-output', compact('tipContent'));
    }

    public function favorite(): void
    {
        $this->model->favorite = !$this->model->favorite;
        $this->model->save();

        //$this->success($this->model->favorite ? 'Tip favorited successfully.' : 'Tip un-favorited successfully.');
        $this->dispatch('tipContentUpdated');

        $this->close();
    }

    public function delete(): void
    {
        $this->model->delete();

        //$this->success('Tip deleted successfully.');
        $this->dispatch('tipContentUpdated');

        $this->close();
    }

    public function close(): void
    {
        try {
            Window::close('tipView');
        } catch (Exception) {
        } finally {
            closeWindow('tip');
        }
    }
}
