<?php

namespace App\Livewire\Pages;

use Livewire\Attributes\Title;
use Livewire\Component;

class TipsNotifier extends Component
{
    protected $listeners = ['apiKeysUpdated' => '$refresh'];

    #[Title('Tips Notifier')]
    public function render()
    {
        return view('livewire.pages.tips-notifier');
    }
}
