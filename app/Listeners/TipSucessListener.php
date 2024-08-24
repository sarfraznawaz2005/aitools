<?php

namespace App\Listeners;

use App\Events\OnNotificationClicked;
use App\Events\TipSucessEvent;
use App\LLM\LlmProvider;
use App\Models\Tip;
use Illuminate\Support\Str;
use Livewire\Features\SupportEvents\HandlesEvents;
use Native\Laravel\Facades\Window;
use Native\Laravel\Notification;

class TipSucessListener
{
    use HandlesEvents;

    public function handle(TipSucessEvent $event): void
    {
        $tip = $event->tip;

        $titleLimits = 200; // to avoid too much context window
        $existingTipContents = implode('', $tip->contents()->latest()->take($titleLimits)->pluck('title')->toArray());

        // remove empty lines
        $existingTipContents = implode(PHP_EOL, array_map(function ($line) {
                return $line . '.';
            }, array_filter(
                array_map(
                    'trim', explode(PHP_EOL, str_replace(["\r", "\n", "\r\n"], PHP_EOL, $existingTipContents))
                ), 'strlen'))
        );


        $llm = getLLM($tip->apiKey);

        $prompt = str_ireplace('{{PROMPT}}', $tip->prompt, config('prompts.tips'));
        $prompt = str_ireplace('{{DISALLOWED}}', $existingTipContents, $prompt);

        $result = $llm->chat($prompt);
        //Log::info($prompt);

        if ($result) {
            $this->generateTitle($llm, $tip, $result);

            //Window::open()->route('test');
            //OnNotificationClicked::broadcast();
            //$this->dispatch(OnNotificationClicked::class);

            Notification::new()
                //->event(OnNotificationClicked::class)
                ->title('✅ AiTools - ' . ucwords($tip->name))
                ->message(Str::limit($result))
                ->show();
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
