<?php

namespace App\Livewire\Chat;

use App\Models\Conversation;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Collection;
use Livewire\Component;

class Sidebar extends Component
{
    public ?Conversation $conversation = null;
    public Collection $conversations;

    public function boot(): void
    {
        $this->conversations = Conversation::all()->sortByDesc('id');
    }

    public function render(): View|Application|Factory
    {
        return view('livewire.chat.sidebar');
    }
}
