<?php

namespace App\Livewire\Chat;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class ChatList extends Component
{
    public Conversation $conversation;
    public Collection $messages;
    public ?Message $lastMessage = null;

    public function mount($conversation)
    {
        $this->conversation = $conversation;
        $this->messages = $conversation->messages->sortByDesc('id');
    }

    #[On('loadConversation')]
    public function loadConversation($conversationId)
    {
        Log::info('loadConversation: ' . $conversationId);

        $this->conversation = Conversation::find($conversationId);
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
