<?php

namespace App\Livewire\Settings;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Sajadsdi\LaravelSettingPro\Support\Setting;

class ChatBuddySettings extends Component
{
    #[Validate('required|min:1|max:365')]
    public int $chatBuddyDeleteOldDays;

    public function mount(): void
    {
        if (Setting::select('ChatBuddy')->has('chatBuddyDeleteOldDays')) {
            $this->chatBuddyDeleteOldDays = Setting::select('ChatBuddy')->get('chatBuddyDeleteOldDays');
        } else {
            $this->chatBuddyDeleteOldDays = 30;
        }
    }

    public function saveChatBuddyOptions(): void
    {
        $this->validate();

        Setting::select('ChatBuddy')->set('chatBuddyDeleteOldDays', $this->chatBuddyDeleteOldDays);

        session()->flash('message', 'Settings saved successfully.');
    }

    public function render(): Application|View|Factory
    {
        return view('livewire.settings.chat-buddy-settings');
    }
}
