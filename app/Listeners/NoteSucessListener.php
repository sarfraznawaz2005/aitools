<?php

namespace App\Listeners;

use App\Events\NoteSucessEvent;
use App\Events\OnNoteNotificationShown;
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
}
