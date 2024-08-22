<?php

namespace App\Livewire\ApiKeys;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Component;

class ApiKeyBanner extends Component
{
    public function render(): View|Factory|Application
    {
        $hasApiKeys = hasApiKeysCreated();

        return view('livewire.apikeys.api-key-banner', compact('hasApiKeys'));
    }
}
