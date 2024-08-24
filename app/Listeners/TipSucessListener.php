<?php

namespace App\Listeners;

use App\Events\TipSucessEvent;
use App\LLM\LlmProvider;
use App\Models\Tip;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Native\Laravel\Notification;

class TipSucessListener
{
    public function handle(TipSucessEvent $event): void
    {
        $tip = $event->tip;

        $titleLimits = 100; // to avoid too much context window
        $existingTipContents = implode("\n", $tip->contents->limit($titleLimits)->pluck('title')->toArray());

        $llm = getLLM($tip->apiKey);

        $prompt = config('prompts.tips');
        $prompt = str_ireplace('{{PROMPT}}', $tip->prompt, $prompt);
        $prompt = str_ireplace('{{DISALLOWED}}', $existingTipContents, $prompt);

        $result = $llm->chat($prompt);
        Log::info($prompt);

        if ($result) {
            $this->generateTitle($llm, $tip, $result);

            Notification::new()
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