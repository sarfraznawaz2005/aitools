<?php

namespace App\Livewire\General;

use Livewire\Component;

class Header extends Component
{
    public string $title;

    public function mount($title): void
    {
        $this->title = $title;
    }
}
