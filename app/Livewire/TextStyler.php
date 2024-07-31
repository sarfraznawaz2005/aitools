<?php

namespace App\Livewire;

use Livewire\Attributes\Title;
use Livewire\Component;

class TextStyler extends Component
{
    #[Title('Text Styler')]
    public function render()
    {
        return view('livewire.text-styler');
    }
}
