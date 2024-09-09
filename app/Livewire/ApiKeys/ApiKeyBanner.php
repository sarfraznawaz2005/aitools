<?php

namespace App\Livewire\ApiKeys;

use Livewire\Attributes\Computed;
use Livewire\Component;

class ApiKeyBanner extends Component
{
    protected $listeners = ['apiKeysUpdated' => '$refresh'];

    #[Computed]
    public function hasApiKeys()
    {
        return hasApiKeysCreated();
    }
}
