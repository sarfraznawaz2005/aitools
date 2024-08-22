<?php

namespace App\Livewire\General;

use App\Models\ApiKey;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Sajadsdi\LaravelSettingPro\Support\Setting;

class ModelSelector extends Component
{
    public string $for = '';
    public string $classes = '';

    public string $selectedModel;

    protected $listeners = ['apiKeysUpdated' => '$refresh'];

    #[Computed]
    public function apiKeys()
    {
        if (
            Setting::select($this->for)->has('selectedModel') &&
            ApiKey::where('model_name', Setting::select($this->for)->get('selectedModel'))->exists()
        ) {
            $this->selectedModel = Setting::select($this->for)->get('selectedModel');
        } else {
            if (ApiKey::hasApiKeys()) {
                $this->selectedModel = ApiKey::whereActive()->first()->model_name;

                Setting::select($this->for)->set('selectedModel', $this->selectedModel);
            }
        }

        return ApiKey::all()->sortBy('model_name');
    }

    public function updated(): void
    {
        Setting::select($this->for)->set('selectedModel', $this->selectedModel);

        $this->dispatch('modelChanged', $this->selectedModel);
    }
}
