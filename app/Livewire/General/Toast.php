<?php

namespace App\Livewire\General;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\On;
use Livewire\Component;

class Toast extends Component
{
    public string $message;
    public string $type;

    #[On('toast-message')]
    public function sendToast($details)
    {
        $this->message = $details['message'];
        $this->type = $details['style'];
    }

    public function render(): View|Application|Factory
    {
        return view('livewire.general.toast');
    }
}
