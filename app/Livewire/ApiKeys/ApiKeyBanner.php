<?php

namespace App\Livewire\ApiKeys;

use Livewire\Attributes\Computed;
use Livewire\Component;

class ApiKeyBanner extends Component
{
    #[Computed]
    public function hasApiKeys()
    {
        return hasApiKeysCreated();
    }
}
