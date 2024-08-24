<?php

namespace App\Livewire\Chat;

use App\Models\Bot;
use App\Models\Conversation;
use App\Traits\InteractsWithToast;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ChatInput extends Component
{
    use InteractsWithToast;

    #[Validate('min:1')]
    public string $query = '';

    public ?Bot $bot = null;

    public ?Conversation $conversation = null;

    #[On('botSelected')]
    public function botSelected(Bot $bot): void
    {
        $this->bot = $bot;
    }

    public function save(): void
    {
        $this->validate();

        if (!$this->query) {
            return;
        }

        if (!$this->bot) {
            $this->bot = Bot::where('name', 'General')->first();
        }

        // create new conversation if not exists
        if (!$this->conversation) {

            $this->conversation = Conversation::create([
                'bot_id' => $this->bot->id,
            ]);

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

            $this->redirect(route('chat-buddyload-conversation', $this->conversation), true);

            return;
        }

        $this->conversation->addChatMessage($this->query);

        $this->dispatch('inputSaved');
        $this->dispatch('conversationsUpdated')->to(Sidebar::class);

        $this->reset('query');
    }

    public function render(): Application|View|Factory
    {
        $this->dispatch('focusInput');

        return view('livewire.chat.chat-input');
    }
}
