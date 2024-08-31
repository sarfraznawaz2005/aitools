<?php

namespace App\Listeners;

use App\Events\NoteSucessEvent;
use App\Events\OnNoteNotificationShown;
use App\LLM\LlmProvider;
use App\Models\Tip;
use Illuminate\Support\Str;
use Native\Laravel\Facades\Settings;
use Native\Laravel\Notification;

class NoteSucessListener
{
    public function handle(NoteSucessEvent $event): void
    {
        $note = $event->note;

        Settings::set('lastNotification', [
            'window' => 'note',
            'route' => 'note-window',
            'routeParams' => ['id' => $note->id]
        ]);

        sleep(1);

        Notification::new()
            ->title('✅ AiTools - ' . ucwords($note->title))
            ->message(Str::limit($note->content))
            ->show();

        OnNoteNotificationShown::broadcast($note->id);
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
