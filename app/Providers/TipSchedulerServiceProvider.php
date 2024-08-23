<?php

namespace App\Providers;

use App\Models\Tip;
use Exception;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class TipSchedulerServiceProvider extends ServiceProvider
{
    public function boot(Schedule $schedule): void
    {
        try {
            $tips = Tip::all();

            foreach ($tips as $tip) {
                $schedule->call(function () use ($tip) {

                    if ($tip->active) {
                        Log::info("Running tip $tip->id");
                    }

                })->cron($tip->cron);
            }
        } catch (Exception) {
            Log::error('Error running tips');
        }
    }
}
