<?php

namespace App\Livewire\Notes;

use App\Models\NoteFolder;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ChatSideBar extends Component
{
    #[Validate('min:1')]
    public string $userMessage = '';

    public array $conversation = [];

    public function mount(): void
    {
        $this->conversation = session('chat_conversation', []);
    }

    public function sendMessage(): void
    {
        $this->dispatch('focusInput');

        $this->validate();

        if (empty(trim($this->userMessage))) {
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

        // Save conversation to session
        session(['chat_conversation' => $this->conversation]);

        // Clear user message
        $this->userMessage = '';
    }

    private function getAIResponse($message)
    {
        // TODO: Implement AI response logic
        return 'AI response for: ' . $message;
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
