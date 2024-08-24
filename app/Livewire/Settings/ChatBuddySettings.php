<?php

namespace App\Livewire\Settings;

use App\Models\Conversation;
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

    public function deleteAllConversations(): void
    {
        Conversation::query()->where('favorite', false)->delete();

        session()->flash('conversationsDeleted', 'Conversations deleted successfully.');

        $this->redirect(route('chat-buddy'));
    }
}
