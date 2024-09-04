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

        if (!is_dir($this->path) || !is_writable($this->path) || file_exists($this->path)) {
            $this->addError('path', 'The path is not valid or not writable.');
            return;
        }

        Settings::set('settings.database-backup-path', trim($this->path));

        session()->flash('message', 'Settings saved successfully.');
    }
}
