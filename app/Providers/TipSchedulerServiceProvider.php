<?php

namespace App\Providers;

use App\Events\TipFailureEvent;
use App\Events\TipSucessEvent;
use App\Models\Tip;
use Exception;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;

class TipSchedulerServiceProvider extends ServiceProvider
{
    public function boot(Schedule $schedule): void
    {
        $tips = Tip::all();

        try {
            foreach ($tips as $tip) {
                if ($tip->active) {
                    $schedule->call(fn() => TipSucessEvent::broadcast($tip))
                        ->name($tip->name)
                        ->withoutOverlapping()
                        ->cron($tip->cron)
                        ->onFailure(fn() => TipFailureEvent::broadcast($tip));
                }
            }
        } catch (Exception) {
            TipFailureEvent::broadcast($tip);
        }
    }
}
