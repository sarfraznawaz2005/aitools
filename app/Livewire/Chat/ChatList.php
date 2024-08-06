<?php

namespace App\Livewire\Chat;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

class ChatList extends Component
{
    public Conversation $conversation;
    public Collection $messages;
    public ?Message $lastMessage = null;

    public function loadMessages($conversation): void
    {
        $this->conversation = $conversation;
        $this->messages = $conversation->messages->sortBy('id');
    }

    public function mount($conversation): void
    {
        $this->loadMessages($conversation);
    }

    #[On('loadConversation')]
    public function loadConversation($conversationId): void
    {
        $this->loadMessages(Conversation::find($conversationId));
    }

    public function placeholder(): string
    {
        return <<<'HTML'
        <div>
            <!-- Loading spinner... -->
            WAITING
        </div>
        HTML;
    }

    public function render()
    {
        return view('livewire.chat.chat-list');
    }
}
