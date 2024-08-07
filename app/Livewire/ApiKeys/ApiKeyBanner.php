<?php

namespace App\Livewire\ApiKeys;

use Livewire\Component;

class ApiKeyBanner extends Component
{
    public function render()
    {
        $hasApiKeys = hasApiKeysCreated();

        return view('livewire.apikeys.api-key-banner', compact('hasApiKeys'));
    }
}
