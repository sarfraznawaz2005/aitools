<?php

namespace App\Livewire\Settings;

use Exception;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Native\Laravel\Facades\Settings;

class Others extends Component
{
    #[Validate('required|numeric|min:300|max:1920')]
    public int $width = 1250;
    #[Validate('required|numeric|min:400|max:1080')]
    public int $height = 750;
    public bool $alwaysOnTop = false;
    public string $page = 'home';

    public function mount(): void
    {
        try {
            $this->loadSettings();
        } catch (Exception) {
            $this->resetToDefaults();
        }
    }

    public function save(): void
    {
        $this->validate();

        $this->saveSettings();

        session()->flash('message', 'Settings saved successfully.');
    }

    public function restore(): void
    {
        $this->resetToDefaults();

        $this->saveSettings();

        session()->flash('message', 'Settings restored successfully.');
    }

    private function loadSettings(): void
    {
        $this->width = Settings::get('settings.width', $this->width);
        $this->height = Settings::get('settings.height', $this->height);
        $this->alwaysOnTop = Settings::get('settings.alwaysOnTop', false);
        $this->page = Settings::get('settings.page', 'home');
    }

    private function saveSettings(): void
    {
        Settings::set('settings.width', $this->width);
        Settings::set('settings.height', $this->height);
        Settings::set('settings.alwaysOnTop', $this->alwaysOnTop);
        Settings::set('settings.page', $this->page);
    }

    private function resetToDefaults(): void
    {
        $this->reset();
    }
}

