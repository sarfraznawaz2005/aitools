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
    #[Validate('required|min:3')]
    public string $query;

    public ?Conversation $conversation = null;

    public function save(): void
    {
        $this->validate();

        // create new conversation if not exists
        if (!$this->conversation) {
            $this->conversation = Conversation::create();
        }

        $this->conversation->addInput($this->query);

        $this->dispatch('userQueryReceived')->to(ChatList::class);

        $this->reset();
    }

    public function render(): Application|View|Factory
    {
        return view('livewire.chat.chat-input');
    }
}
