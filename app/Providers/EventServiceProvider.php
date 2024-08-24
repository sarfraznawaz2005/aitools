<?php

namespace App\Providers;

use App\Events\OnNotificationShown;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Native\Laravel\Facades\Window;

class EventServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Event::listen(OnNotificationShown::class, function () {
            //openWindow('main', 'tips-notifier');
            //Window::close('main');

            Window::open('tip')
                ->route('tips-notifier')
                ->showDevTools(false)
                //->frameless()
                //->titleBarHidden()
                //->fullscreen(true)
                ->width(600)
                ->maxWidth(600)
                ->height(500)
                ->maxHeight(500)
                ->lightVibrancy()
                ->hasShadow()
                ->closable()
                ->focusable(false)
                ->resizable(false)
                ->minimizable(false)
                ->maximizable(false);
        });
    }
}
