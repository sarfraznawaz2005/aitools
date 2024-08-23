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
                if ($tip->active) {

                    $schedule->call(function () use ($tip) {
                        // do something
                    })
                        ->cron($tip->cron)
                        ->onSuccess(function () use ($tip) {
                            Log::info("Tip {$tip->name} ran successfully");
                        })
                        ->onFailure(function () use ($tip) {
                            Log::error("Tip {$tip->name} failed to run");
                        });
                }
            }
        } catch (Exception) {
            Log::error('Error running tips');
        }
    }
}
