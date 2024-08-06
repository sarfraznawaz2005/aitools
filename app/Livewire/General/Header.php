<?php

namespace App\Livewire\General;

use Livewire\Component;

class Header extends Component
{
    public $title;

    public function mount($title)
    {
        $this->title = $title;
    }

    public function render()
    {
        return view('livewire.general.header');
    }
}
