<?php

namespace App\Livewire\Pages;

use App\Constants;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Sajadsdi\LaravelSettingPro\Support\Setting;

class TextStyler extends Component
{
    #[Validate('required|min:25')]
    public string $text = '';

    protected $listeners = ['apiKeysUpdated' => '$refresh'];

    public function getText(string $prompt): void
    {
        $this->validate();

        Setting::select(Constants::TEXTSTYLER_SELECTED_LLM_KEY)->set('prompt', $prompt);
        Setting::select(Constants::TEXTSTYLER_SELECTED_LLM_KEY)->set('text', $this->text);

        $this->dispatch('getTextStylerAiResponse');
    }

    #[Title('Text Styler')]
    public function render(): View|Factory|Application
    {
        return view('livewire.pages.text-styler');
    }
}
