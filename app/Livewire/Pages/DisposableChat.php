<?php

namespace App\Livewire\Pages;

use App\Constants;
use App\Models\Bot;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Renderless;
use Livewire\Attributes\Session;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DisposableChat extends Component
{
    #[Validate('min:1')]
    public string $userMessage = '';

    #[Session(key: 'quick-conversation')]
    public array $conversation = [];

    protected $listeners = ['refreshQuickChat' => '$refresh'];

    public bool $loaded = false;

    public function load(): void
    {
        $this->loaded = true;
    }

    public function placeholder(): string
    {
        return '
            <div class="flex justify-center items-center h-full w-full z-[1000]">
                <svg class="animate-spin h-12 w-12 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
            </div>
    ';
    }

    #[Layout('components/layouts/headerless')]
    public function render(): View|Factory|Application
    {
        return view('livewire.pages.disposable-chat');
    }

    #[On('suggestedAnswer')]
    function suggestedAnswer(string $linkText): void
    {
        $this->setMessage($linkText);
    }

    public function regenerate(int $index): void
    {
        $lastMessageTimestamp = $this->conversation[$index]['timestamp'];
        unset($this->conversation[$index]);

        $lastMessage = end($this->conversation);
        $lastMessage['timestamp'] = $lastMessageTimestamp;

        $this->dispatch('goAhead');
    }

    public function setMessage(string $message): void
    {
        $this->userMessage = $message;

        $this->validate();

        if (empty(trim($this->userMessage))) {
            $this->addError('userMessage', 'Please enter a message.');
            $this->dispatch('focusInput');
            return;
        }

        // Add user message to conversation
        $this->conversation[] = [
            'role' => 'user',
            'content' => $this->userMessage,
            'timestamp' => time(),
        ];

        $this->dispatch('goAhead');
    }

    #[On('getResponse')]
    public function getResponse(): void
    {
        $this->resetValidation();
        $this->resetErrorBag();

        $aiResponse = $this->getAIResponse($this->userMessage);

        // Add AI response to conversation
        $this->conversation[] = [
            'role' => 'ai',
            'content' => $aiResponse,
            'timestamp' => time(),
        ];

        // Clear user message
        $this->userMessage = '';

        $this->dispatch('focusInput');
    }

    private function getAIResponse($userMessage): string
    {
        $llm = getSelectedLLMProvider(Constants::NOTES_SELECTED_LLM_KEY);

        try {

            $messages = getMessages($this->conversation);
            $conversationHistory = implode("\n", array_map(fn($message) => htmlToText($message), $messages));

            $prompt = makePromptQuickChat($userMessage, $conversationHistory, 2);

            $consolidatedResponse = '';

            $llm->chat($prompt, true, function ($chunk) use (&$consolidatedResponse) {
                $consolidatedResponse .= $chunk;

                usleep(300000); // 0.3 seconds

                $this->stream(
                    to: 'aiStreamResponse',
                    content: $chunk,
                );
            });

            return processMarkdownToHtml($consolidatedResponse);
        } catch (Exception $e) {
            //$message = $e->getMessage() . ' on line ' . $e->getLine() . ' in ' . $e->getFile();
            $message = $e->getMessage();
            $error = '<span class="text-red-600 text-xs">Oops! Failed to get a response due to some error, please try again, error: ' . $message . '</span>';

            $this->stream(
                to: 'aiStreamResponse',
                content: $error,
            );

            return $error;
        }
    }

    #[Renderless]
    public function deleteMessage($index): void
    {
        unset($this->conversation[$index]);

        // Re-index the array to maintain consistency
        $this->conversation = array_values($this->conversation);

        $this->dispatch('refreshQuickChat');
    }

    public function resetConversation(): void
    {
        $this->conversation = [];

        $this->dispatch('focusInput');
    }

    #[Renderless]
    public function export($format): StreamedResponse
    {
        $filename = 'quick-chat-export' . '.' . $format;

        $content = '<meta charset="utf-8"><div style="margin:50px;">';

        if ($format === 'txt') {
            $content .= str_repeat('-', 100);
        }

        foreach ($this->conversation as $message) {
            $body = trim($message['content']);

            if ($message['role'] === 'ai') {
                $content .= <<<HTML
<div style='border-radius: 10px; border: 1px solid #555; padding: 15px; margin-bottom: 25px;'>
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
}
