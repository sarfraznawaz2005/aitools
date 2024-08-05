<?php

namespace App\Livewire;

use App\Models\ApiKey;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Component;

class ModelSelector extends Component
{
    public $selectedModel;

    protected $listeners = [
        'apiKeysUpdated' => '$refresh'
    ];

    public function mount(): void
    {
        if (
            session('selectedModel') &&
            ApiKey::where('model_name', session('selectedModel'))->exists()
        ) {
            $this->selectedModel = session('selectedModel');
        } else {
            if (ApiKey::hasApiKeys()) {
                $this->selectedModel = ApiKey::where('active', true)->first()->model_name;

                session()->put('selectedModel', $this->selectedModel);
            }
        }
    }

    public function updated(): void
    {
        session()->put('selectedModel', $this->selectedModel);
    }

    public function render(): Application|View|Factory
    {
        return view('livewire.model-selector', [
            'apiKeys' => ApiKey::all()->sortBy('model_name'),
        ]);
    }
}
