<?php

namespace App\Livewire\Chat;

use App\Models\Conversation;
use Illuminate\Support\Collection;
use Livewire\Component;

class Sidebar extends Component
{
    public Collection $conversations;

    public function boot(): void
    {
        $this->conversations = Conversation::all()->sortByDesc('updated_at');
    }

    public function render()
    {
        return view('livewire.chat.sidebar');
    }
}
