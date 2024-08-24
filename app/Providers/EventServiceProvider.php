<?php

namespace App\Providers;

use App\Events\OnNotificationShown;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
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

            sleep(1);

            //Window::close('main');

            Window::open('tip')
                ->url("/tip-content?contentid=$event->id")
                ->showDevTools(false)
                //->frameless()
                //->titleBarHidden()
                //->fullscreen(true)
                ->width(1280)
                ->hideMenu()
                ->minWidth(1024)
                ->height(800)
                ->minHeight(800)
                ->lightVibrancy()
                ->hasShadow()
                ->rememberState()
                ->focusable(false)
                //->closable(true)
                ->minimizable(false)
                ->maximizable(false);
        });
    }
}
