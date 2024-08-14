<?php

namespace App\Livewire\Chat;

use App\Constants;
use App\Models\Conversation;
use App\Models\Message;
use App\Traits\InteractsWithToast;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Application;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ChatList extends Component
{
    use InteractsWithToast;

    public ?Conversation $conversation = null;
    public ?Message $lastMessage = null;
    public Collection $messages;

    protected $listeners = ['refreshChatList' => '$refresh'];

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
        if ($this->conversation) {
            $this->messages = $this->conversation->messages->sortBy('id');
        }
    }

    public function deleteMessage(Message $message): void
    {
        if ($message->delete()) {
            // doing redirect because otherwise was getting 404 for some reason on multiple random deletes
            $this->redirect(route(config('tools.chat-buddy.route') . 'load-conversation', $this->conversation), true);
        } else {
            $this->danger('Failed to delete message.');
        }
    }

    public function regenerate(Message $message): void
    {
        $message->body = Constants::CHATBUDDY_LOADING_STRING;
        $message->save();

        $this->refresh();

        $this->dispatch('getAiResponse', $this->conversation->id);
    }

    public function export($format): StreamedResponse
    {
        $filename = 'chat-' . strtolower(Str::slug($this->conversation->title)) . '.' . $format;

        $content = '<div style="margin:50px;">';
        $content .= '<div align="center"><h2 style="margin-bottom: 0">Conversation Name: ' . $this->conversation->title . '</h2></div><br>';
        $content .= '<div align="center"><strong>Created On: ' . $this->conversation->created_at . '</strong></div><br>';

        if ($format === 'txt') {
            $content .= str_repeat('-', 100);
        }

        foreach ($this->messages as $message) {
            $body = trim($message->body);

            if ($message->is_ai) {
                $content .= <<<HTML
<div style='border-radius: 10px; border: 1px solid #555; padding: 15px; margin-bottom: 25px;'>
<strong>AI - $message->llm:</strong>
<hr>
$body
</div>
HTML;
            } else {
                $content .= <<<HTML
<div style='border-radius: 10px; border: 1px solid #555; padding: 15px; margin-bottom: 25px; background: #dbeafe;'>
<strong>User:</strong>
<hr>
$body
</div>
HTML;

            }

            if ($format === 'txt') {
                $content .= str_repeat('-', 100);
            }
        }

        $content .= '</div>';

        $content = trim($content);

        if ($format === 'txt') {
            $content = htmlToText($content, false);
        }

        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, $filename);
    }

    public function render(): View|Application|Factory
    {
        $this->refresh();

        return view('livewire.chat.chat-list');
    }
}
