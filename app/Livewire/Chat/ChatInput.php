<?php

namespace App\Livewire\Chat;

use App\Models\Conversation;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ChatInput extends Component
{
    #[Validate('min:2')]
    public string $query = '';

    public ?Conversation $conversation = null;

    public function save(): void
    {
        $this->validate();

        if (!$this->query) {
            return;
        }

        // create new conversation if not exists
        if (!$this->conversation) {
            $this->conversation = Conversation::create();

            // for new conversation, we need to generate a title
            $this->conversation->generateTitle($this->query);

            $this->redirect(route(config('tools.chat-buddy.route') . 'load-conversation', $this->conversation), true);
        } else {
            $this->conversation->addInput($this->query);

            $this->dispatch('userQueryReceived')->to(ChatList::class);
        }

        $this->reset('query');

        $this->dispatch('querySubmitted');
    }

    public function render(): Application|View|Factory
    {
        return view('livewire.chat.chat-input');
    }
}
