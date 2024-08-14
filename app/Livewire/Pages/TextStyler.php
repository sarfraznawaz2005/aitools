<?php

namespace App\Livewire\Pages;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

class TextStyler extends Component
{
    #[Validate('required|min:25')]
    public string $text = '';

    protected $listeners = ['apiKeysUpdated' => '$refresh'];

    public function getText(string $style)
    {

    }

    #[Title('Text Styler')]
    public function render(): View|Factory|Application
    {
        return view('livewire.pages.text-styler');
    }
}
