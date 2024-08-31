<?php

namespace App\Providers;

use App\Events\TipFailureEvent;
use App\Events\TipSucessEvent;
use App\Models\Tip;
use Exception;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class ScheduleServiceProvider extends ServiceProvider
{
    public function boot(Schedule $schedule): void
    {
        // note reminders
        $schedule->command('app:send-note-reminders')->everyMinute();

        // tips notifier reminders
        $this->tipsNotifierReminders($schedule);
    }

    private function tipsNotifierReminders(Schedule $schedule): void
    {
        if (!Schema::hasTable('tips')) {
            return;
        }

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
