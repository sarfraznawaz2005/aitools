<?php

namespace App\Livewire\Pages;

use App\Constants;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Session;
use Livewire\Attributes\Validate;
use Livewire\Component;

class DisposableChat extends Component
{
    #[Validate('min:1')]
    public string $userMessage = '';

    #[Session(key: 'quick-conversation')]
    public array $conversation = [];

    protected $listeners = ['refreshQuickChat' => '$refresh'];

    #[Layout('components/layouts/headerless')]
    public function render(): View|Factory|Application
    {
        return view('livewire.pages.disposable-chat');
    }

    #[On('suggestedAnswer')]
    function suggestedAnswer(string $linkText): void
    {
        $this->userMessage = $linkText;

        $this->dispatch('goAhead');
    }

    public function regenerate(int $index): void
    {
        unset($this->conversation[$index]);

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

        $this->dispatch('goAhead');
    }

    #[On('getResponse')]
    public function getResponse(): void
    {
        if (trim($this->userMessage)) {
            // Add user message to conversation
            $this->conversation[] = [
                'role' => 'user',
                'content' => $this->userMessage,
                'timestamp' => time(),
            ];
        }

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
            $conversationHistory = implode("\n", $messages);

            $prompt = makePromptQuickChat($userMessage, $conversationHistory, 2);

            $consolidatedResponse = '';

            $llm->chat($prompt, true, function ($chunk) use (&$consolidatedResponse) {
                $consolidatedResponse .= $chunk;

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
}
