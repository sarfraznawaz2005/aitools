<?php

namespace App\Livewire;

use App\Models\ApiKey;
use Livewire\Attributes\Title;
use Livewire\Component;

class ChatBuddy extends Component
{
    public $selectedModel;

    protected $listeners = ['apiKeysUpdated' => '$refresh'];

    public function mount(): void
    {
        $this->setSelectedModel();
    }

    /**
     * @param mixed $selectedModel
     */
    public function setSelectedModel(): void
    {
        $this->selectedModel = $this->selectedModel ?? ApiKey::getDefaultModel();
    }

    #[Title('Chat Buddy')]
    public function render()
    {
        return view('livewire.chat-buddy', [
            'apiKeys' => ApiKey::all()->sortBy('name'),
        ]);
    }
}
