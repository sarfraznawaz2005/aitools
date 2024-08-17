<?php

namespace App\Livewire\Chat;

use App\Models\Bot;
use App\Traits\InteractsWithToast;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Component;

class BotSelector extends Component
{
    use InteractsWithToast;

    public Bot $model;

    public string $name;
    public string $bio;
    public string $prompt;
    public string $icon;
    public string $type;

    protected $listeners = ['refreshBot' => '$refresh'];

    protected function rules(): array
    {
        return [
            'name' => 'required|min:5|max:25|unique:bots,name,' . ($this->model->id ?? 'NULL') . ',id',
            'bio' => 'required|min:5|max:500',
            'prompt' => 'required|min:3|max:2500',
            'icon' => 'required',
            'type' => 'required',
        ];
    }

    public function mount(Bot $bot = null): void
    {
        $this->model = $bot ?? new Bot();

        $this->fill($this->model->toArray());
    }

    public function selectBot(Bot $bot): void
    {
        $this->dispatch('botSelected', $bot->id);
    }

    public function save(): void
    {
        $this->validate();

        $this->model->fill([
            'name' => $this->name,
            'bio' => $this->bio,
            'prompt' => $this->prompt,
            'icon' => $this->icon,
            'type' => $this->type,
        ])->save();

        //dd($this->model);
        //$this->dispatch('refreshBot');

        session()->flash('message', $this->model->wasRecentlyCreated ? 'Bot created successfully!' : 'Bot saved successfully!');

        if ($this->model->wasRecentlyCreated) {
            $this->dispatch('hideModal', ['id' => 'botModal']);
            $this->resetForm();
        }
    }

    public function edit(Bot $bot): void
    {
        $this->resetErrorBag();

        $this->model = $bot;

        $this->fill($bot->toArray());
    }

    public function delete(Bot $bot): void
    {
        $bot->delete();

        $this->dispatch('hideModal', ['id' => 'botModal']);

        $this->success('Bot deleted successfully!');

        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->reset();
        $this->resetErrorBag();

        $this->model = new Bot();
    }

    public function render(): View|Factory|Application
    {
        return view('livewire.chat.bot-selector', [
            'bots' => Bot::query()->orderBy('name')->get(),
        ]);
    }
}
