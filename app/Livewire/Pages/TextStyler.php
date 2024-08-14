<?php

namespace App\Livewire\Pages;

use App\Constants;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

class TextStyler extends Component
{
    #[Validate('required|min:25')]
    public string $text = '';
    public string $output = '';

    protected $listeners = ['apiKeysUpdated' => '$refresh'];

    public function getText(string $style): void
    {
        $this->validate();

        $llm = getSelectedLLMProvider(Constants::TEXTSTYLER_SELECTED_LLM_KEY);

        $prompt = config('text-styler.' . $style);

        $result = $llm->chat($prompt . $this->text);

        $this->output = $result;
    }

    #[Title('Text Styler')]
    public function render(): View|Factory|Application
    {
        return view('livewire.pages.text-styler');
    }
}
