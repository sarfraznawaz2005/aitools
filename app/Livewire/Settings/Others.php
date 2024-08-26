<?php

namespace App\Livewire\Settings;

use Livewire\Attributes\Validate;
use Livewire\Component;
use Native\Laravel\Facades\Settings;

class Others extends Component
{
    #[Validate('required|numeric|min:800|max:1920')]
    public int $width = 1280;
    #[Validate('required|numeric|min:600|max:1080')]
    public int $height = 800;
    public bool $alwaysOnTop = false;
    public string $page = 'home';

    public function mount(): void
    {
        $this->loadSettings();
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
        $this->width = Settings::get('settings.width', 1280);
        $this->height = Settings::get('settings.height', 800);
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
        $this->width = 1280;
        $this->height = 800;
        $this->alwaysOnTop = false;
        $this->page = 'home';
    }
}

