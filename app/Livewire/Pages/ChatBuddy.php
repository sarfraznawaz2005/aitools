<?php

namespace App\Livewire\Pages;

use App\Livewire\General\Toast;
use App\Models\Conversation;
use App\Traits\InteractsWithToast;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

class ChatBuddy extends Component
{
    use InteractsWithToast;

    public Conversation $conversation;

    #[On('toast')]
    public function sendToast($type, $message)
    {
//            $this->dispatch(
//                'toast',
//                'danger',
//                'This field is required and must be at least 3 characters long.')
//                ->to(ChatBuddy::class);
//
        $this->$type($message);
    }

    #[Title('Chat Buddy')]
    public function render(): Application|View|Factory
    {
        return view('livewire.pages.chat-buddy');
    }
}
