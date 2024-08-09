<?php

namespace App\Livewire\Chat;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Application;
use Livewire\Attributes\On;
use Livewire\Component;

class ChatList extends Component
{
    public ?Conversation $conversation = null;
    public Collection $messages;
    public ?Message $lastMessage = null;

    protected $listeners = ['inputSaved' => '$refresh'];

    #[On('inputSaved')]
    public function refreshMessagesByInput(): void
    {
        $this->messages = $this->conversation->messages->sortBy('id');
        $this->lastMessage = $this->conversation->messages->last();

        $this->addToChatUi($this->lastMessage);

        //$this->dispatch('message-refreshed-by-input')->self();
    }

    /**
     * Method for adding a new message to the chat.
     *
     * @param Message $message
     * @return void
     */
    public function addToChatUi(Message $message): void
    {
        $this->messages->prepend($message);
    }

    public function mount($conversation = null): void
    {
        if ($conversation) {
            $this->conversation = $conversation;
            $this->messages = $conversation->messages->sortBy('id');

            // Update last access time
            $conversation->updated_at = now();
            $conversation->save();
        }
    }

    public function render(): View|Application|Factory
    {
        return view('livewire.chat.chat-list');
    }
}
