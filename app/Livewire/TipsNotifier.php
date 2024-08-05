<?php

namespace App\Livewire;

use Livewire\Attributes\Title;
use Livewire\Component;

class TipsNotifier extends Component
{
    protected $listeners = ['apiKeysUpdated' => '$refresh'];

    #[Title('Tips Notifier')]
    public function render()
    {
        return view('livewire.tips-notifier');
    }
}
