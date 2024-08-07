<?php

namespace App\Livewire\Pages;

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
        return view('livewire.pages.chat-buddy');
    }
}
