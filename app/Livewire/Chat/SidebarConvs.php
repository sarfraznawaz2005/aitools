<?php

namespace App\Livewire\Chat;

use App\Models\Conversation;
use App\Traits\InteractsWithToast;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class SidebarConvs extends Component
{
    use InteractsWithToast;

    public ?Conversation $conversation = null;

    public bool $archived;

    public string $search = '';

    protected $listeners = ['conversationsUpdated' => '$refresh'];

    public bool $loaded = false;
    public Collection $conversations;

    public function load(): void
    {
        $this->conversations = Conversation::query()
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%');
            })
            ->when($this->archived, function ($query) {
                $query->where('archived', true);
            }, function ($query) {
                $query->where('archived', false);
            })
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->get();

        $this->loaded = true;
    }

    public function placeholder(): string
    {
        return '
        <div class="flex justify-center items-center h-full w-full">
            <svg class="animate-spin h-10 w-10 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
            </svg>
        </div>
    ';
    }


    public function toggleFavorite(Conversation $conversation): void
    {
        $conversation->favorite = !$conversation->favorite;
        $conversation->save();

        $message = $conversation->favorite ? 'Conversation favorited successfully.' : 'Conversation un-favorited successfully.';
        $this->success($message);

        $this->dispatch('conversationsUpdated');
    }

    public function toggleArchived(Conversation $conversation): void
    {
        $conversation->archived = !$conversation->archived;
        $conversation->save();

        $this->redirect(route('chat-buddy'), true);
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
            $this->redirect(route('chat-buddy'), true);
        } else {
            $this->dispatch('conversationsUpdated');

            $this->success('Conversation deleted successfully.');
        }
    }
}
