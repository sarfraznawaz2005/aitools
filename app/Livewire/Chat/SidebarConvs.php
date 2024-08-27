<?php

namespace App\Livewire\Chat;

use App\Models\Conversation;
use App\Traits\InteractsWithToast;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class SidebarConvs extends Component
{
    use InteractsWithToast;

    public ?Conversation $conversation = null;

    public bool $archived;

    public string $search = '';

    protected $listeners = ['conversationsUpdated' => '$refresh'];

    #[Computed]
    public function conversations(): Collection
    {
        return Conversation::query()
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
