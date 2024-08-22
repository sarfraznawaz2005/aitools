<?php

namespace App\Providers;

use App\Models\Tip;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class TipSchedulerServiceProvider extends ServiceProvider
{
    public function boot(Schedule $schedule): void
    {
        $tips = Tip::all();

        foreach ($tips as $tip) {
            $expression = $this->buildCronExpression($tip);
            $schedule->call(function () use ($tip) {
                Log::info('Tip triggered: ' . $tip->tip);
                // Implement the logic to send or display the tip
            })->cron($expression);
        }
    }

    protected function buildCronExpression(Tip $tip): string
    {
        if ($tip->schedule_type === 'every_minute') {
            return '* * * * *';
        }

        if ($tip->schedule_type === 'hourly') {
            return '0 * * * *';
        }

        if ($tip->schedule_type === 'daily') {
            return '0 0 * * *';
        }

        if ($tip->schedule_type === 'weekly') {
            return '0 0 * * ' . ($tip->day_of_week ?? 0);
        }

        if ($tip->schedule_type === 'monthly') {
            return '0 0 ' . ($tip->day_of_month ?? 1) . ' * *';
        }

        if ($tip->schedule_type === 'custom') {
            $minute = $tip->minute ?? '*';
            $hour = $tip->hour ?? '*';
            $dayOfMonth = $tip->day_of_month ?? '*';
            $month = $tip->month ?? '*';
            $dayOfWeek = $tip->day_of_week ?? '*';

            return "{$minute} {$hour} {$dayOfMonth} {$month} {$dayOfWeek}";
        }

        return '* * * * *';
    }
}
