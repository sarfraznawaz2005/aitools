<?php

namespace App\Livewire\Chat;

use App\Models\Conversation;
use App\Traits\InteractsWithToast;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Collection;
use Livewire\Component;

class Sidebar extends Component
{
    use InteractsWithToast;

    public ?Conversation $conversation = null;
    public Collection $conversations;

    protected $listeners = ['conversationsUpdated' => '$refresh'];

    public function boot(): void
    {
        $this->conversations = Conversation::all()->sortByDesc('id');
    }

    public function render(): View|Application|Factory
    {
        return view('livewire.chat.sidebar');
    }

    public function rename(Conversation $conversation, $title): void
    {
        $conversation->update(['title' => $title]);

        $this->success('Conversation re-named successfully.');

        $this->dispatch('conversationsUpdated');
    }

    public function delete(Conversation $conversation): void
    {
        $conversation->delete();

        $this->success('Conversation deleted successfully.');

        $this->dispatch('conversationsUpdated');
    }
}
