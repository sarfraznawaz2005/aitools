<?php

namespace App\Livewire\Notes;

use App\Constants;
use App\Models\Note;
use App\Models\NoteFolder;
use App\Services\NotesSearchService;
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

    public string $aiStreamResponse = '';

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

            $searchService = NotesSearchService::getInstance($llm, 2000);
            $results = $searchService->searchTexts($notes, $userMessage);
            //dd($results);

            $context = '';

            foreach ($results as $result) {
                $text = $result['matchedChunk']['text'];

                // Collect unique sources
                $sources = [];
                foreach ($result['matchedChunk']['metadata'] as $metadata) {
                    $title = $metadata['title'];
                    $source = $metadata['source'];
                    $sources[$title] = $source; // Use title as the key to ensure uniqueness
                }

                $formattedSources = [];
                foreach ($sources as $title => $source) {
                    $formattedSources[] = "'$title' ($source)";
                }

                $metaDataString = 'Sources: ' . implode(', ', $formattedSources);

                $context .= $text . "\n\n<sources>" . $metaDataString . "</sources>\n\n";
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
            $error = '<span class="text-red-600 text-xs">Oops! Failed to get a response due to some error, please try again.' . ' ' . $e->getMessage() . '</span>';

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

    #[Computed]
    private function notes(): array
    {
        return Note::with('folder')->get()->map(function ($note) {
            return [
                'id' => $note->id,
                'title' => $note->title,
                'content' => $note->content,
                'folder' => $note->folder->name,
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
