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
            $result = $this->conversation->generateTitle($this->query);

            if ($error = AIChatFailed($result)) {
                $this->conversation->delete();
                $this->conversation = null;
                $this->danger($error);
                return;
            }

            $this->conversation->addChatMessage($this->query);

            $this->conversation->createTempAImessage();

            session()->flash('addBotMessage', $this->conversation->id);

            $this->redirect(route(config('tools.chat-buddy.route') . 'load-conversation', $this->conversation), true);

            return;
        }

        $this->conversation->addChatMessage($this->query);

        $this->dispatch('inputSaved');

        $this->reset('query');
    }

    public function render(): Application|View|Factory
    {
        return view('livewire.chat.chat-input');
    }
}
