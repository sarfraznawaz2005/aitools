<?php

namespace App\Livewire\Pages;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Title;
use Livewire\Component;

class SmartNotes extends Component
{
    #[Title('Smart Notes')]
    public function render(): View|Application|Factory
    {
        return view('livewire.pages.smart-notes');
    }
}
