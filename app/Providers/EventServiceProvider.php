<?php

namespace App\Providers;

use App\Events\OnNotificationShown;
use App\Services\NotificationManager;
use Exception;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Native\Laravel\Events\Notifications\NotificationClicked;
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

        Event::listen(NotificationClicked::class, function () {
            $lastNotification = NotificationManager::getLastNotification();

            if ($lastNotification) {
                openWindow($lastNotification['window'], $lastNotification['route'], $lastNotification['routeParams']);

                NotificationManager::clearLastNotification();
            }
        });
    }
}
