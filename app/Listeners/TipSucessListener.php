<?php

namespace App\Listeners;

use App\Events\TipSucessEvent;
use Native\Laravel\Notification;

class TipSucessListener
{
    public function handle(TipSucessEvent $event): void
    {
        $tip = $event->tip;
        $existingTipContents = implode("\n", $tip->contents->pluck('title')->toArray());

        $llm = getLLM($tip->apiKey);

        $prompt = config('prompts.tips');
        $prompt = str_ireplace('{{PROMPT}}', $tip->prompt, $prompt);
        $prompt = str_ireplace('{{DISALLOWED}}', $existingTipContents, $prompt);

        $result = $llm->chat($prompt);

        Notification::new()
            ->title('✅ AiTools - ' . ucwords($tip->name))
            ->message($result)
            ->show();
    }
}
