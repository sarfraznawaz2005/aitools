<?php

namespace App\Listeners;

use App\Events\TipFailureEvent;
use Native\Laravel\Notification;

class TipFailureListener
{
    public function handle(TipFailureEvent $event): void
    {
        Notification::new()
            ->title('🛑 ' . $event->tip->name)
            ->message("[{$event->tip->name}] failed to run.")
            ->show();
    }
}
