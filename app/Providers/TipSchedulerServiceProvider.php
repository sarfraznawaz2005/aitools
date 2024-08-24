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
        try {
            $tips = Tip::all();

            foreach ($tips as $tip) {
                if ($tip->active) {

                    $schedule->call(fn() => $this->processTip($tip))
                        ->name($tip->name)
                        ->withoutOverlapping()
                        ->timezone('Asia/Karachi')
                        ->cron($tip->cron)
                        ->onSuccess(fn() => TipSucessEvent::broadcast($tip))
                        ->onFailure(fn() => TipFailureEvent::broadcast($tip));
                }
            }
        } catch (Exception) {
            TipFailureEvent::broadcast($tip);
        }
    }

    private function processTip($tip): true
    {
        return true;
    }
}
