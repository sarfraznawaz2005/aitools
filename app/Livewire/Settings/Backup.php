<?php

namespace App\Livewire\Settings;

use Livewire\Attributes\Validate;
use Livewire\Component;
use Native\Laravel\Facades\Settings;

class Backup extends Component
{
    #[Validate('required|min:2')]
    public string $path = '';

    public function mount(): void
    {
        $this->path = Settings::get('settings.database-backup-path', '');
    }

    public function save(): void
    {
        $this->validate();

        // because Settings facade does not work in cli.
        // Settings::set('settings.database-backup-path', trim($this->path));

        file_put_contents(storage_path('settings-database-backup-path'), trim($this->path));

        session()->flash('message', 'Settings saved successfully.');
    }
}
