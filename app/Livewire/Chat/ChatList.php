<?php

namespace App\Livewire\Chat;

use App\Constants;
use App\Models\Bot;
use App\Models\Conversation;
use App\Models\Message;
use App\Traits\InteractsWithToast;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ChatList extends Component
{
    use InteractsWithToast;

    public ?Conversation $conversation = null;
    public ?Message $lastMessage = null;
    public array $botFiles = [];

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
        $this->dispatch('conversationsUpdated')->to(Sidebar::class);

        $this->refresh();

        $this->dispatch('getChatBuddyAiResponse', $this->conversation->id);
    }

    #[Computed]
    public function messages()
    {
        if ($this->conversation) {
            return $this->conversation->messages->sortBy('id');
        }
    }

    protected function refresh(): void
    {
        if ($this->conversation) {

            if (!$this->conversation->bot) {
                $this->conversation->bot()->associate(Bot::query()->where('name', 'General')->first());
                $this->conversation->save();

                session()->flash('message', 'The Bot was not found, so the conversation was assigned to the General Bot automatically.');
            }

            if ($this->conversation->bot->isDocumentBot()) {
                $this->botFiles = $this->conversation->bot->files();
            }
        }
    }

    #[On('modelChanged')]
    function modelChanged(): void
    {
        if ($this->conversation && $this->conversation->bot->isDocumentBot()) {
            $files = $this->conversation->bot->files();

            foreach ($files as $file) {
                $fileName = basename($file);
                $path = storage_path("app/$fileName-" . $this->conversation->id . '.json');
                @unlink($path);
            }
        }
    }

    public function forceAnswer(Message $message): void
    {
        touch('forceAnswer');

        $message->delete();

        $this->dispatch('createTempAImessage')->self();
    }

    #[On('suggestedAnswerClicked')]
    function suggestedAnswerClicked(string $linkText): void
    {
        $this->conversation->addChatMessage($linkText);

        $this->dispatch('createTempAImessage')->self();
    }

    public function deleteMessage(Message $message): void
    {
        if ($message->delete()) {
            // doing redirect because otherwise was getting 404 for some reason on multiple random deletes
            $this->redirect(route('chat-buddyload-conversation', $this->conversation), true);
        } else {
            $this->danger('Failed to delete message.');
        }
    }

    public function clearConversation(): void
    {
        if ($this->conversation->messages()->delete()) {
            // doing redirect because otherwise was getting 404 for some reason on multiple random deletes
            $this->redirect(route('chat-buddyload-conversation', $this->conversation), true);
        }
    }

    public function regenerate(Message $message): void
    {
        $message->body = Constants::CHATBUDDY_LOADING_STRING;
        $message->updated_at = now();
        $message->save();

        $this->dispatch('conversationsUpdated')->to(Sidebar::class);

        $this->refresh();

        $this->dispatch('getChatBuddyAiResponse', $this->conversation->id);
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
