<?php

namespace App\Livewire;

use App\Models\ApiKey;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

class ChatBuddy extends Component
{
    public $selectedModel;

    protected $listeners = [
        'apikeys-updated' => 'refreshComponent'
    ];

    #[On('apikeys-updated')]
    public function refreshComponent()
    {
        Log::info('apiKeysUpdated event received');
        $this->render();
    }

    public function mount()
    {
        session()->flash('message', 'API key saved successfully!API key saved successfully!');
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
            'apiKeys' => ApiKey::all()->sortBy('model_name'),
        ]);
    }
}
