<?php

namespace App\Livewire;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Title;
use Livewire\Component;

class ChatBuddy extends Component
{
    #[Title('Chat Buddy')]
    public function render(): Application|View|Factory
    {
        return view('livewire.chat-buddy');
    }
}
