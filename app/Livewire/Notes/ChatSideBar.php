<?php

namespace App\Livewire\Notes;

use App\Constants;
use App\Models\ApiKey;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Native\Laravel\Facades\Settings;

class ChatSideBar extends Component
{
    public string $selectedModel;

    protected $listeners = [
        'noteChatModelChanged' => '$refresh',
        'apiKeysUpdated' => '$refresh'
    ];

    public function mount(): void
    {
        $this->getOrSetCurrentModel();
    }

    #[Computed]
    public function apiKeys()
    {
        $this->getOrSetCurrentModel();

        return ApiKey::all()->sortBy('model_name');
    }

    public function setModel(string $model): void
    {
        Settings::set(Constants::NOTES_SELECTED_LLM_KEY . '.selectedModel', $model);

        $this->dispatch('noteChatModelChanged');
    }

    /**
     * @return void
     */
    public function getOrSetCurrentModel(): void
    {
        $selectedModel = Settings::get(Constants::NOTES_SELECTED_LLM_KEY . '.selectedModel');

        if ($selectedModel && ApiKey::where('model_name', $selectedModel)->exists()) {
            $this->selectedModel = $selectedModel;
        } else {
            if (ApiKey::hasApiKeys()) {
                $this->selectedModel = ApiKey::whereActive()->first()->model_name;
                Settings::set(Constants::NOTES_SELECTED_LLM_KEY . '.selectedModel', $this->selectedModel);
            }
        }
    }
}
