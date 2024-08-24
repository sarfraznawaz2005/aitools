<?php

namespace App\Providers;

use App\Events\OnNotificationShown;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Native\Laravel\Client\Client;
use Native\Laravel\Events\Windows\WindowMinimized;
use Native\Laravel\Facades\Window;

class EventServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Event::listen(OnNotificationShown::class, function () {
            //Log::info('Opening Tips Window');

            sleep(1);

            openWindow('tip', 'tips-notifier');
            //Window::close('main');
        });

        Event::listen(WindowMinimized::class, function () {
           //
        });
    }
}
