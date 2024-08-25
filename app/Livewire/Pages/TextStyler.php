<?php

namespace App\Livewire\Pages;

use App\Constants;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Native\Laravel\Facades\Settings;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TextStyler extends Component
{
    #[Validate('required|min:25')]
    public string $text = '';

    protected $listeners = ['apiKeysUpdated' => '$refresh'];

    public function getText(string $prompt): void
    {
        $this->validate();

        Settings::set(Constants::TEXTSTYLER_SELECTED_LLM_KEY . '.prompt', base64_encode($prompt));
        Settings::set(Constants::TEXTSTYLER_SELECTED_LLM_KEY . '.text', base64_encode($this->text));

        $this->dispatch('getTextStylerAiResponse');
    }

    public function chat(): StreamedResponse
    {
        return response()->stream(function () {

            try {

                if (Constants::TEST_MODE) {
                    sleep(1);

                    sendStream(Constants::TEST_MESSAGE);

                    return;
                }

                $prompt = Settings::get(Constants::TEXTSTYLER_SELECTED_LLM_KEY . '.prompt');
                $prompt = base64_decode($prompt);

                $text = Settings::get(Constants::TEXTSTYLER_SELECTED_LLM_KEY . '.text');
                $text = base64_decode($text);

                $prompt = $prompt . "\n" . '"' . $text . '"';
                //Log::info($prompt);

                $llm = getSelectedLLMProvider(Constants::TEXTSTYLER_SELECTED_LLM_KEY);

                $llm->chat($prompt, true, function ($chunk) {
                    sendStream(nl2br($chunk));
                });

            } catch (Exception $e) {
                Log::error(__CLASS__ . ': ' . $e->getMessage());
                $error = '<span class="text-red-600">Oops! Failed to get a response, please try again.' . ' ' . $e->getMessage() . '</span>';

                sendStream($error);
            } finally {
                sendStream("", true);
            }

        }, 200, [
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Content-Type' => 'text/event-stream',
        ]);
    }

    #[Title('Text Styler')]
    public function render(): View|Factory|Application
    {
        return view('livewire.pages.text-styler');
    }
}
