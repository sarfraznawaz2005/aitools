<?php

namespace App\Livewire\Chat;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Application;
use Livewire\Component;

class ChatList extends Component
{
    public ?Conversation $conversation;
    public Collection $messages;
    public ?Message $lastMessage = null;

    public function loadMessages($conversation): void
    {
        $this->conversation = $conversation;
        $this->messages = $conversation->messages->sortBy('id');
    }

    public function mount($conversation = null): void
    {
        if ($conversation) {
            $this->loadMessages($conversation);
        }
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

    public function render(): View|Application|Factory
    {
        return view('livewire.chat.chat-list');
    }
}
