<?php

namespace App\Actions;

use App\Constants;
use Exception;
use Illuminate\Support\Facades\Log;
use Sajadsdi\LaravelSettingPro\Support\Setting;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TextStylerAction
{
    public function __invoke(): StreamedResponse
    {
        $llm = getSelectedLLMProvider(Constants::TEXTSTYLER_SELECTED_LLM_KEY);

        return response()->stream(function () use ($llm) {

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
                $text = Setting::select(Constants::TEXTSTYLER_SELECTED_LLM_KEY)->get('text');

                $prompt = $prompt . "\n" . '"' . $text . '"';
                //Log::info($prompt);

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
}
