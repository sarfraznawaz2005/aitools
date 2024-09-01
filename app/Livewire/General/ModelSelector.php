<?php

namespace App\Livewire\General;

use App\Models\ApiKey;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Native\Laravel\Facades\Settings;

class ModelSelector extends Component
{
    public string $for = '';
    public string $classes = '';

    public string $selectedModel;

    protected $listeners = ['apiKeysUpdated' => '$refresh'];

    public function boot(): void
    {
        $selectedModel = Settings::get($this->for . '.selectedModel');

        if ($selectedModel && ApiKey::where('model_name', $selectedModel)->exists()) {
            $this->selectedModel = $selectedModel;
        } else {
            if (ApiKey::hasApiKeys()) {
                $this->selectedModel = ApiKey::whereActive()->first()->model_name;
                Settings::set($this->for . '.selectedModel', $this->selectedModel);
            }
        }
    }

    #[Computed]
    public function apiKeys()
    {
        return ApiKey::all()->sortBy('model_name');
    }

    public function updated(): void
    {
        Settings::set($this->for . '.selectedModel', $this->selectedModel);

        $this->dispatch('modelChanged', $this->selectedModel);
    }
}
