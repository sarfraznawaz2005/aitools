<?php

namespace App\Livewire\Pages;

use Livewire\Attributes\Title;
use Livewire\Component;

class TextStyler extends Component
{
    protected $listeners = ['apiKeysUpdated' => '$refresh'];

    #[Title('Text Styler')]
    public function render()
    {
        return view('livewire.pages.text-styler');
    }
}
