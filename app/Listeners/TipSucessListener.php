<?php

namespace App\Listeners;

use App\Events\OnTipNotificationShown;
use App\Events\OnTipContentSaved;
use App\Events\TipSucessEvent;
use App\LLM\LlmProvider;
use App\Models\Tip;
use Illuminate\Support\Str;
use Native\Laravel\Facades\Settings;
use Native\Laravel\Notification;

class TipSucessListener
{
    public function handle(TipSucessEvent $event): void
    {
        $tip = $event->tip;

        $titleLimits = 100; // to avoid too much context window
        $existingTipContents = implode('', $tip->contents()->latest()->take($titleLimits)->pluck('title')->toArray());

        // remove empty lines
        $existingTipContents = implode(PHP_EOL, array_map(function ($line) {
                return $line . '.';
            }, array_filter(array_map(
                'trim', explode(PHP_EOL, str_replace(["\r", "\n", "\r\n"], PHP_EOL, $existingTipContents))
            ), 'strlen'))
        );


        $llm = getLLM($tip->apiKey);

        $prompt = str_ireplace('{{PROMPT}}', $tip->prompt, config('prompts.tips'));
        $prompt = str_ireplace('{{DISALLOWED}}', $existingTipContents, $prompt);

        $isError = false;

        try {
            $result = $llm->chat($prompt);
            //Log::info($prompt);
        } catch (\Exception $e) {
            $result = $e->getMessage();
            $isError = true;
        } finally {

            if ($result) {
                sleep(1);

                if ($isError) {
                    Notification::new()
                        ->title('❌ AiTools - ' . ucwords($tip->name))
                        ->message(AIChatFailed($result))
                        ->show();

                    return;
                }

                $this->generateTitle($llm, $tip, $result);

                Settings::set('lastNotification', [
                    'window' => 'tip',
                    'route' => 'tip-window',
                    'routeParams' => ['id' => $tip->contents()->latest()->take(1)->first()->id]
                ]);

                sleep(1);

                Notification::new()
                    ->title('✅ AiTools - ' . ucwords($tip->name))
                    ->message(Str::limit($result))
                    ->show();

                OnTipNotificationShown::broadcast($tip->contents()->latest()->take(1)->first()->id);
                OnTipContentSaved::broadcast();
            }
        }

    }

    private function generateTitle(LlmProvider $llm, Tip $tip, string $result): void
    {
        $resultCleaned = htmlToText($result);

        $prompt = "
        Create only a single title of max 50 characters from the provided Text, title must be of minimum 50 characters
        and must not be more than 50 characters and without punctuation characters, language must be same as Text.

        Make sure title you generate is single and not more than 50 characters.

        Text: '$resultCleaned'
        ";

        $title = $llm->chat($prompt);

        $tip->contents()->create([
            'title' => $title ?? '',
            'content' => $result,
        ]);
    }
}
