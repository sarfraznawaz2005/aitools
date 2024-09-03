<?php

namespace App\Livewire\Notes;

use App\Models\NoteFolder;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Session;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ChatSideBar extends Component
{
    #[Validate('min:1')]
    public string $userMessage = '';

    #[Session(key: 'notes-conversation')]
    public array $conversation = [];

    public function sendMessage(): void
    {
        $this->dispatch('focusInput');

        $this->validate();

        if (empty(trim($this->userMessage))) {
            $this->addError('userMessage', 'Please enter a message.');
            return;
        }

        // Add user message to conversation
        $this->conversation[] = [
            'role' => 'user',
            'content' => $this->userMessage,
            'timestamp' => now()->format('g:i A')
        ];

        // TODO: Process the message and get AI response
        $aiResponse = $this->getAIResponse($this->userMessage);

        // Add AI response to conversation
        $this->conversation[] = [
            'role' => 'ai',
            'content' => $aiResponse,
            'timestamp' => now()->format('g:i A')
        ];

        // Clear user message
        $this->userMessage = '';

        $this->dispatch('focusInput');
    }

    private function getAIResponse($message)
    {
        // TODO: Implement AI response logic
        return 'AI response for: ' . $message;
    }

    public function resetConversation(): void
    {
        $this->conversation = [];
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
