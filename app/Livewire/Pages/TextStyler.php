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
use Sajadsdi\LaravelSettingPro\Support\Setting;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TextStyler extends Component
{
    #[Validate('required|min:25')]
    public string $text = '';

    protected $listeners = ['apiKeysUpdated' => '$refresh'];

    public function getText(string $prompt): void
    {
        $this->validate();

        Setting::select(Constants::TEXTSTYLER_SELECTED_LLM_KEY)->set('prompt', base64_encode($prompt));
        Setting::select(Constants::TEXTSTYLER_SELECTED_LLM_KEY)->set('text', base64_encode($this->text));

        $this->dispatch('getTextStylerAiResponse');
    }

    public function chat(): StreamedResponse
    {
        return response()->stream(function () {

            try {

                if (Constants::TEST_MODE) {
                    sleep(1);

                    echo "event: update\n";
                    echo "data: " . json_encode(Constants::TEST_MESSAGE) . "\n\n";
                    ob_flush();
                    flush();

                    return;
                }

                $prompt = Setting::select(Constants::TEXTSTYLER_SELECTED_LLM_KEY)->get('prompt');
                $prompt = base64_decode($prompt);

                $text = Setting::select(Constants::TEXTSTYLER_SELECTED_LLM_KEY)->get('text');
                $text = base64_decode($text);

                $prompt = $prompt . "\n" . '"' . $text . '"';
                //Log::info($prompt);

                $llm = getSelectedLLMProvider(Constants::TEXTSTYLER_SELECTED_LLM_KEY);

                $llm->chat($prompt, true, function ($chunk) {
                    echo "event: update\n";
                    echo "data: " . json_encode(nl2br($chunk)) . "\n\n";
                    ob_flush();
                    flush();
                });

            } catch (Exception $e) {
                Log::error(__CLASS__ . ': ' . $e->getMessage());

                echo "event: update\n";
                echo "data: " . json_encode(Constants::AI_ERROR_MESSSAGE) . "\n\n";
                ob_flush();
                flush();
            } finally {
                echo "event: update\n";
                echo "data: <END_STREAMING_SSE>\n\n";
                ob_flush();
                flush();
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
