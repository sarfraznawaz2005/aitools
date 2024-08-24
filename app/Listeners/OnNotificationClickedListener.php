<?php

namespace App\Listeners;

use App\Events\OnNotificationClicked;
use Native\Laravel\Facades\Window;

class OnNotificationClickedListener
{
    public function handle(OnNotificationClicked $event): void
    {
        if (!Window::current()->isVisible()) {
            Window::open();
        }

        Window::current()->focus();
    }
}
