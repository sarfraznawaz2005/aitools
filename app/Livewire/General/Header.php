<?php

namespace App\Livewire\General;

use Livewire\Attributes\On;
use Livewire\Attributes\Renderless;
use Livewire\Component;

class Header extends Component
{
    public string $title;
    public bool $showHeader = true;

    #[On('hideHeader')]
    #[Renderless]
    public function hideHeader(): void
    {
        $this->showHeader = false;
    }

    #[Renderless]
    public function mount($title): void
    {
        $this->title = $title;
    }
}
