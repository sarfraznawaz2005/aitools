<?php

namespace App\Livewire\Chat;

use App\Models\Bot;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Component;

class BotSelector extends Component
{
    public function selectBot(Bot $bot): void
    {
        $this->dispatch('botSelected', $bot->id);
    }

    public function render(): View|Factory|Application
    {
        return view('livewire.chat.bot-selector', [
            'bots' => Bot::query()->orderBy('name')->get(),
        ]);
    }
}
