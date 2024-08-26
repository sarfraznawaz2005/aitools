<?php

namespace App\Providers;

use App\Events\OnNotificationShown;
use App\Services\NotificationManager;
use Exception;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Native\Laravel\Events\Notifications\NotificationClicked;
use Native\Laravel\Events\Windows\WindowMinimized;
use Native\Laravel\Facades\Settings;
use Native\Laravel\Facades\Window;

class EventServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Event::listen(WindowMinimized::class, function () {
            //
        });

        Event::listen(OnNotificationShown::class, function ($event) {
            try {
                Window::close('tip');
            } catch (Exception) {
            } finally {
                openWindow('tip', 'tip-content', ['id' => $event->id]);
            }
        });

        Event::listen(NotificationClicked::class, function () {
            $lastNotification = Settings::get('lastNotification');

            if ($lastNotification) {
                try {
                    Window::close($lastNotification['window']);
                } catch (Exception) {
                } finally {
                    openWindow($lastNotification['window'], $lastNotification['route'], $lastNotification['routeParams']);

                    Settings::set('lastNotification', null);
                }
            }
        });
    }
}
