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

    public function render(): View|Application|Factory
    {
        $this->conversations = Conversation::query()
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->get();

        return view('livewire.chat.sidebar');
    }

    public function toggleFavorite(Conversation $conversation): void
    {
        if ($conversation->favorite) {
            $conversation->favorite = false;
            $conversation->save();
            $this->success('Conversation un-favorited successfully.');
        } else {
            $conversation->favorite = true;
            $conversation->save();
            $this->success('Conversation favorited successfully.');
        }

        $this->dispatch('conversationsUpdated');
    }

    public function rename(Conversation $conversation, $title): void
    {
        if (trim($conversation->title) === trim($title)) {
            return;
        }

        if (!trim($title)) {
            return;
        }

        if (strlen($title) < 4 || strlen($title) > 25) {
            $this->danger('Conversation title must be between 4 to 25 characters.');
            return;
        }

        $conversation->update(['title' => $title]);

        $this->dispatch('conversationsUpdated');

        $this->success('Conversation re-named successfully.');
    }

    public function delete(Conversation $conversation): void
    {
        $conversation->delete();

        // if it is active conversation, we redirect instead to avoid 404
        if ($this->conversation && $this->conversation->id === $conversation->id) {
            $this->redirect(route(config('tools.chat-buddy.route')), true);
        } else {
            $this->dispatch('conversationsUpdated');

            $this->success('Conversation deleted successfully.');
        }
    }
}
