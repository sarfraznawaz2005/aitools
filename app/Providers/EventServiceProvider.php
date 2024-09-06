<?php

namespace App\Providers;

use App\Events\OnNoteNotificationShown;
use App\Events\OnTipNotificationShown;
use App\Events\QuickChatClicked;
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

        Event::listen(QuickChatClicked::class, function () {
            openWindow('quick-chat', 'quick-chat', [], true, true, true, true, 800, 600);
        });

        Event::listen(OnTipNotificationShown::class, function ($event) {
            try {
                Window::close('tip');
            } catch (Exception) {
            } finally {
                openWindow('tip', 'tip-window', ['id' => $event->id]);
            }
        });

        Event::listen(OnNoteNotificationShown::class, function ($event) {
            try {
                Window::close('note');
            } catch (Exception) {
            } finally {
                openWindow(
                    'note', 'note-window', ['id' => $event->id],
                    true, true, true, true, 1024, 700
                );
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
