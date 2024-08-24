<?php

namespace App\Listeners;

use App\Events\TipSucessEvent;
use Native\Laravel\Notification;

class TipSucessListener
{
    public function handle(TipSucessEvent $event): void
    {
        Notification::new()
            ->title('✅ ' . $event->tip->name)
            ->message("[{$event->tip->name}] ran successfully.")
            ->show();
    }
}
