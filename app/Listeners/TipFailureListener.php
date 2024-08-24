<?php

namespace App\Listeners;

use App\Events\TipFailureEvent;
use Native\Laravel\Notification;

class TipFailureListener
{
    public function handle(TipFailureEvent $event): void
    {
        $name = ucwords($event->tip->name);

        Notification::new()
            ->title("🛑 AiTools - $name")
            ->message("$name failed to run.")
            ->show();
    }
}
