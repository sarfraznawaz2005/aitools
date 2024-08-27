<?php

namespace App\Livewire\Chat;

use App\Models\Conversation;
use Livewire\Component;

class Sidebar extends Component
{
    public ?Conversation $conversation = null;

    protected $listeners = ['conversationsUpdated' => '$refresh'];
}
