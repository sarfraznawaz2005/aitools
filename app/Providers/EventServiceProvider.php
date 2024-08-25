<?php

namespace App\Providers;

use App\Events\OnNotificationClicked;
use App\Events\OnNotificationShown;
use Exception;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Native\Laravel\Events\Windows\WindowMinimized;
use Native\Laravel\Facades\Window;

class EventServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Event::listen(WindowMinimized::class, function () {
            //
        });

        Event::listen(OnNotificationShown::class, function ($event) {
            //Log::info('Opening Tips Window');

            try {
                Window::close('tip');
            } catch (Exception) {
            } finally {
                openWindow('tip', 'tip-content', ['id' => $event->id]);
            }
        });

        Event::listen(OnNotificationClicked::class, function ($event) {
            Log::info('Opening Tips Window');

            try {
                Window::close('tip');
            } catch (Exception) {
            } finally {
                openWindow('tip', 'tip-content', ['id' => $event->id]);
            }
        });
    }
}
