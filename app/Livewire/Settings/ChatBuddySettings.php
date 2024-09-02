<?php

namespace App\Livewire\Settings;

use App\Models\Conversation;
use Exception;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Native\Laravel\Facades\Settings;

class ChatBuddySettings extends Component
{
    #[Validate('required|min:1|max:365')]
    public int $chatBuddyDeleteOldDays;

    public function mount(): void
    {
        try {
            $this->chatBuddyDeleteOldDays = Settings::get('ChatBuddy.chatBuddyDeleteOldDays', 30);
        } catch (Exception) {
            $this->chatBuddyDeleteOldDays = 30;
        }
    }

    public function saveChatBuddyOptions(): void
    {
        $this->validate();

        Settings::set('ChatBuddy.chatBuddyDeleteOldDays', $this->chatBuddyDeleteOldDays);

        session()->flash('message', 'Settings saved successfully.');
    }

    public function deleteAllConversations(): void
    {
        Conversation::query()->where('favorite', false)->delete();

        session()->flash('conversationsDeleted', 'Conversations deleted successfully.');

        $this->redirect(route('chat-buddy'));
    }
}
