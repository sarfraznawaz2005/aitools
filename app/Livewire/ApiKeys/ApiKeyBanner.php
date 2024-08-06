<?php

namespace App\Livewire\ApiKeys;

use App\Models\ApiKey;
use Livewire\Component;

class ApiKeyBanner extends Component
{
    public function render()
    {
        $hasApiKeys = ApiKey::hasApiKeys();

        return view('livewire.apikeys.api-key-banner', compact('hasApiKeys'));
    }
}
