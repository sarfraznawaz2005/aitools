<?php

namespace App\Providers;

use App\Events\OnNotificationClicked;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Event::listen(OnNotificationClicked::class, function ($event) {
            // openWindow('test', 'test');
        });
    }
}
