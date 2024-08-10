<?php

namespace App\Livewire\Pages;

use App\Models\Conversation;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Title;
use Livewire\Component;

class ChatBuddy extends Component
{
    public Conversation $conversation;

    #[Title('Chat Buddy')]
    public function render(): Application|View|Factory
    {
        if (session()->has('addBotMessage')) {
            $this->dispatch('getAiResponse', session('addBotMessage'));
        }

        return view('livewire.pages.chat-buddy');
    }
}
