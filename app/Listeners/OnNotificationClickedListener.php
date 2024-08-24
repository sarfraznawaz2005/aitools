<?php

namespace App\Listeners;

use App\Events\OnNotificationClicked;
use Native\Laravel\Facades\Window;

class OnNotificationClickedListener
{
    public function handle(OnNotificationClicked $event): void
    {
        openWindow('test', 'test');

        Window::close('main');
    }
}
