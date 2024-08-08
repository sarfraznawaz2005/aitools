<?php

namespace App\Livewire\Chat;

use App\Models\Conversation;
use App\Traits\InteractsWithToast;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ChatInput extends Component
{
    use InteractsWithToast;

    #[Validate('required|min:3')]
    public string $query;

    public ?Conversation $conversation = null;

    public function save(): void
    {
        if (!$this->validate()) {
            $this->danger('This field is required and must be at least 3 characters long.');
            //$this->addError('error', 'Cannot delete the default API key!');
            return;
        }

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
        $this->success('This field is required and must be at least 3 characters long.');
        return view('livewire.chat.chat-input');
    }
}
