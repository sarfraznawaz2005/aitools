<?php

namespace App\Livewire\Settings;

use App\Models\TipContent;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Sajadsdi\LaravelSettingPro\Support\Setting;

class TipsSettings extends Component
{
    #[Validate('required|min:1|max:365')]
    public int $deleteOldDays;

    public function mount(): void
    {
        if (Setting::select('TipsNotifier')->has('deleteOldDays')) {
            $this->deleteOldDays = Setting::select('TipsNotifier')->get('deleteOldDays');
        } else {
            $this->deleteOldDays = 30;
        }
    }

    public function saveOptions(): void
    {
        $this->validate();

        Setting::select('TipsNotifier')->set('deleteOldDays', $this->deleteOldDays);

        session()->flash('message', 'Settings saved successfully.');
    }

    public function deleteAll(): void
    {
        TipContent::query()->where('favorite', false)->delete();

        session()->flash('allDeleted', 'All deleted successfully.');

        $this->redirect(route('tips-notifier'));
    }
}
