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
        if ($conversation->title === $title) {
            return;
        }

        if (!trim($title)) {
            return;
        }

        if (strlen($title) < 5 || strlen($title) > 25) {
            $this->danger('Conversation title must be between 5 to 25 characters.');
            return;
        }

        $conversation->update(['title' => $title]);

        $this->success('Conversation re-named successfully.');

        $this->dispatch('conversationsUpdated');
    }

    public function delete(Conversation $conversation): void
    {
        $conversation->delete();

        // if it is active conversation, we redirect instead to avoid 404
        if ($this->conversation && $this->conversation->id === $conversation->id) {
            $this->redirect(route(config('tools.chat-buddy.route')));
        } else {
            $this->success('Conversation deleted successfully.');

            $this->dispatch('conversationsUpdated');
        }
    }
}
