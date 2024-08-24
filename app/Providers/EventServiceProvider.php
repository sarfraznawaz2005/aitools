<?php

namespace App\Providers;

use App\Events\OnNotificationShown;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Event::listen(OnNotificationShown::class, function () {
            openWindow('tip', 'tips-notifier');
            //Window::close('main');
        });
    }
}
