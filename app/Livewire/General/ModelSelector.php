<?php

namespace App\Livewire\General;

use App\Models\ApiKey;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Component;
use Sajadsdi\LaravelSettingPro\Support\Setting;

class ModelSelector extends Component
{
    public string $for;

    public string $selectedModel;

    protected $listeners = ['apiKeysUpdated' => '$refresh'];

    public function mount(): void
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
    }

    public function updated(): void
    {
        Setting::select($this->for)->set('selectedModel', $this->selectedModel);
    }

    public function render(): Application|View|Factory
    {
        return view('livewire.general.model-selector', [
            'apiKeys' => ApiKey::all()->sortBy('model_name'),
        ]);
    }
}
