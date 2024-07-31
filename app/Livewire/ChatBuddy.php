<?php

namespace App\Livewire;

use Livewire\Attributes\Title;
use Livewire\Component;

class ChatBuddy extends Component
{
    #[Title('Chat Buddy')]
    public function render()
    {
        return view('livewire.chat-buddy');
    }
}
