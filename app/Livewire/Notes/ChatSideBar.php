<?php

namespace App\Livewire\Notes;

use App\Constants;
use App\Models\Note;
use App\Models\NoteFolder;
use App\Services\JsonFileVectorStore;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Session;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ChatSideBar extends Component
{
    #[Validate('min:1')]
    public string $userMessage = '';

    #[Session(key: 'notes-conversation')]
    public array $conversation = [];

    protected $listeners = ['refreshNotesChat' => '$refresh'];

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
        $this->resetValidation();
        $this->resetErrorBag();

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

            $messages = getMessages($this->conversation);

            // remove very last message - user query
            array_pop($messages);

            $conversationHistory = implode("\n", array_map(fn($message) => htmlToText($message), $messages));

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

    #[Computed]
    private function notes(): array
    {
        return Note::with('folder')->get()->map(function ($note) {
            return [
                'id' => $note->id,
                'title' => $note->title,
                'text' => $note->content,
                'source' => "$note->title (" . $note->folder->name . ")",
            ];
        })->toArray();
    }

    public function deleteMessage($index): void
    {
        unset($this->conversation[$index]);

        // Re-index the array to maintain consistency
        $this->conversation = array_values($this->conversation);

        $this->dispatch('refreshNotesChat');
    }

    public function resetConversation(): void
    {
        $this->conversation = [];

        $this->dispatch('focusInput');
    }

    #[Computed]
    public function folders(): Collection
    {
        return NoteFolder::query()->with('notes')->orderBy('name')->get();
    }

    #[Computed]
    public function totalNotesCount(): int
    {
        return NoteFolder::query()->withCount('notes')->get()->sum('notes_count');
    }
}
