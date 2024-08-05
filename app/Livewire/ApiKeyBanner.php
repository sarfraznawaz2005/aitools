<?php

namespace App\Livewire;

use App\Models\ApiKey;
use Livewire\Component;

class ApiKeyBanner extends Component
{
    public function render()
    {
        $hasApiKeys = ApiKey::hasApiKeys();

        return view('livewire.api-key-banner', compact('hasApiKeys'));
    }
}
