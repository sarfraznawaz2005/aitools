<?php

namespace App\Livewire\General;

use Livewire\Attributes\On;
use Livewire\Component;

class Header extends Component
{
    public string $title;
    public bool $showHeader = true;

    #[On('hideHeader')]
    public function hideHeader(): void
    {
        $this->showHeader = false;
    }

    public function mount($title): void
    {
        $this->title = $title;
    }
}
