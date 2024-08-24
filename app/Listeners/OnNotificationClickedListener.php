<?php

namespace App\Listeners;

use App\Events\OnNotificationClicked;
use Illuminate\Support\Facades\Log;
use Native\Laravel\Facades\Window;

class OnNotificationClickedListener
{
    public function handle(OnNotificationClicked $event): void
    {
        Log::info('Notification clicked');
        Window::open()->route('test');
        Window::current()->focus();
        Log::info('DONE: Notification clicked');
    }
}
