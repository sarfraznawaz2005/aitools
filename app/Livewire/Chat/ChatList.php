<?php

namespace App\Livewire\Chat;

use App\Models\Conversation;
use App\Models\Message;
use App\Traits\InteractsWithToast;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Application;
use Livewire\Attributes\On;
use Livewire\Component;

class ChatList extends Component
{
    use InteractsWithToast;

    public ?Conversation $conversation = null;
    public Collection $messages;
    public ?Message $lastMessage = null;

    #[On('inputSaved')]
    public function refreshMessagesByInput(): void
    {
        $this->lastMessage = $this->conversation->messages->last();

        $this->refresh();

        $this->dispatch('createTempAImessage')->self();
    }

    #[On('createTempAImessage')]
    public function createTempAImessage(): void
    {
        // Create temp answer to show the user that the AI is typing
        $this->conversation->createTempAImessage();

        $this->refresh();

        $this->dispatch('getAiResponse', $this->conversation->id);
    }

    protected function refresh(): void
    {
        $this->messages = $this->conversation->messages->sortBy('id');
    }

    public function deleteMessage(Message $message): void
    {
        if ($message->delete()) {
            $this->success('Message deleted successfully.');

            $this->refresh();
        } else {
            $this->danger('Failed to delete message.');
        }
    }

    public function mount($conversation = null): void
    {
        if ($conversation) {
            $this->conversation = $conversation;
            $this->messages = $conversation->messages->sortBy('id');

            // Update last access time
            $conversation->updated_at = now();
            $conversation->save();
        }
    }

    public function render(): View|Application|Factory
    {
        return view('livewire.chat.chat-list');
    }
}
