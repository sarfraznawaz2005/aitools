<?php

namespace App\Livewire\Settings;

use App\Models\TipContent;
use Exception;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Native\Laravel\Facades\Settings;

class TipsSettings extends Component
{
    #[Validate('required|min:1|max:365')]
    public int $deleteOldDays;

    public function mount(): void
    {
        try {
            $this->deleteOldDays = Settings::get('TipsNotifier.deleteOldDays', 30);
        } catch (Exception) {
            $this->deleteOldDays = 30;
        }
    }

    public function saveOptions(): void
    {
        $this->validate();

        Settings::set('TipsNotifier.deleteOldDays', $this->deleteOldDays);

        session()->flash('message', 'Settings saved successfully.');
    }

    public function deleteAll(): void
    {
        TipContent::query()->where('favorite', false)->delete();

        session()->flash('allDeleted', 'All deleted successfully.');

        $this->redirect(route('tips-notifier'));
    }
}
