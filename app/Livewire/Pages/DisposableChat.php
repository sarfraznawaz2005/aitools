<?php

namespace App\Livewire\Pages;

use App\Constants;
use App\Services\JsonFileVectorStore;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Session;
use Livewire\Attributes\Title;
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
    #[Title('Quick Chat')]
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
        // Add user message to conversation
        $this->conversation[] = [
            'role' => 'user',
            'content' => $this->userMessage,
            'timestamp' => time(),
        ];

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

        $notes = $this->notes;

        try {

            $searchService = JsonFileVectorStore::getInstance($llm, 'notes.json', 2000);
            $results = $searchService->searchTexts($notes, $userMessage);
            //dd($results);

            if (!count($results)) {
                $this->stream(
                    to: 'aiStreamResponse',
                    content: Constants::NO_RESULTS_FOUND,
                );

                return Constants::NO_RESULTS_FOUND;
            }

            $context = '';

            foreach ($results as $result) {
                $text = $result['matchedChunk']['text'];
                $context .= $text . "\n\n<sources>" . $result['matchedChunk']['metadata'] . "</sources>\n\n";
            }

            $messages = $this->getMessages();
            $conversationHistory = implode("\n", $messages);

            $prompt = makePromoptForNotes($context, $userMessage, $conversationHistory);

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
            $message = $e->getMessage() . ' on line ' . $e->getLine() . ' in ' . $e->getFile();
            $error = '<span class="text-red-600 text-xs">Oops! Failed to get a response due to some error, please try again, error: ' . $message . '</span>';

            $this->stream(
                to: 'aiStreamResponse',
                content: $error,
            );

            return $error;
        }
    }

    function getMessages(): array
    {
        $uniqueMessages = [];
        $messages = $this->conversation;

        // Strings to filter out from the conversation

        // Sort the array by timestamp in descending order
        usort($messages, function ($a, $b) {
            return $b['timestamp'] - $a['timestamp'];
        });

        // Remove duplicates and empty content entries, and filter by role and content
        $messages = array_values(array_filter(array_unique($messages, SORT_REGULAR), function ($item) {
            $filterOutStrings = [
                "conversation history",
                "have enough information to answer this question accurately",
                "provided context"
            ];

            if ($item['role'] !== 'user') {
                foreach ($filterOutStrings as $str) {
                    if (str_contains(strtolower($item['content']), strtolower($str))) {
                        return false;
                    }
                }
            }

            // Also ensure that the content is not empty
            return !empty($item['content']);
        }));

        // Format and filter unique messages
        foreach ($messages as $message) {
            $formattedMessage = ($message['role'] === 'user' ? 'USER: ' : 'ASSISTANT: ') . $message['content'];

            if (!in_array($formattedMessage, $uniqueMessages)) {
                $uniqueMessages[] = htmlToText($formattedMessage);
            }
        }

        return array_slice($uniqueMessages, 0, Constants::NOTES_TOTAL_CONVERSATION_HISTORY);
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
