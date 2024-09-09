<?php

namespace App\Livewire\General;

use App\Models\ApiKey;
use Exception;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Renderless;
use Livewire\Component;
use Native\Laravel\Facades\Settings;

class ModelSelector extends Component
{
    public string $for = '';
    public string $classes = '';

    public string $selectedModel;

    protected $listeners = [
        'refresh' => '$refresh',
        'apiKeysUpdated' => '$refresh',
    ];

    public bool $loaded = false;

    public function load(): void
    {
        try {
            $selectedModel = Settings::get($this->for . '.selectedModel');

            if ($selectedModel && ApiKey::where('model_name', $selectedModel)->exists()) {
                $this->selectedModel = $selectedModel;
            } else {
                if (ApiKey::hasApiKeys()) {
                    $this->selectedModel = ApiKey::whereActive()->first()->model_name;
                    Settings::set($this->for . '.selectedModel', $this->selectedModel);
                }
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
        } finally {
            $this->loaded = true;
        }
    }

    #[Computed]
    public function apiKeys()
    {
        return ApiKey::all()->sortBy('model_name');
    }

    #[Renderless]
    public function setModel(string $model): void
    {
        Settings::set($this->for . '.selectedModel', $model);

        $this->selectedModel = $model;

        $this->dispatch('refresh');
        $this->dispatch('modelChanged', $model);
    }
}
