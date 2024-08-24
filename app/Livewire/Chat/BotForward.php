<?php

namespace App\Livewire\Chat;

use App\Models\Bot;
use App\Models\Conversation;
use App\Models\Message;
use App\Traits\InteractsWithToast;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class BotForward extends Component
{
    use InteractsWithToast;

    public ?Message $message = null;
    public ?Bot $bot = null;

    #[Computed]
    public function bots(): Collection
    {
        return Bot::query()->orderBy('name')->get();
    }

    #[On('startFoward')]
    public function startFoward(Message $message): void
    {
        $this->message = $message;

        $this->dispatch('showModal', ['id' => 'botForwardModal']);
    }

    public function selectBot(Bot $bot): void
    {
        $this->bot = $bot;

        $this->dispatch('botChosen', $bot->id);
    }

    public function forward(): void
    {
        if (!$this->bot) {
            $this->danger('Please select a bot to forward the message to.');
            return;
        }

        $query = $this->message->body;

        $conversation = Conversation::create([
            'bot_id' => $this->bot->id,
        ]);

        // for new conversation, we need to generate a title
        $result = $conversation->generateTitle(htmlToText($query));

        if ($error = AIChatFailed($result)) {
            $conversation->delete();
            $conversation = null;
            $this->danger($error);
            return;
        }

        $message = str_ireplace(['<br>', '<br/>', '<br />'], "\n\n", $query);
        $message = htmlToText($message);
        $message = "<forwarded_query>$message</forwarded_query>";

        $conversation->addChatMessage($message);

        $conversation->createTempAImessage();

        session()->flash('addBotMessage', $conversation->id);

        $this->redirect(route('chat-buddyload-conversation', $conversation), true);
    }
}
