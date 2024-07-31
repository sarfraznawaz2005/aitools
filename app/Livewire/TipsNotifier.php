<?php

namespace App\Livewire;

use Livewire\Attributes\Title;
use Livewire\Component;

class TipsNotifier extends Component
{
    #[Title('Tips Notifier')]
    public function render()
    {
        return view('livewire.tips-notifier');
    }
}
