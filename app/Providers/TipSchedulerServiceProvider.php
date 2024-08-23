<?php

namespace App\Providers;

use App\Models\Tip;
use Exception;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Native\Laravel\Notification;

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
                        ->onSuccess(function () use ($tip) {
                            Log::info("✅ [{$tip->name}] ran successfully.");

                            Notification::new()
                                ->title('✅ ' . $tip->name)
                                ->message("[{$tip->name}] ran successfully.")
                                ->show();
                        })
                        ->onFailure(function () use ($tip) {
                            Log::error("🛑 [{$tip->name}] failed to run.");

                            Notification::new()
                                ->title('🛑 ' . $tip->name)
                                ->message("[{$tip->name}] failed to run.")
                                ->show();
                        });
                }
            }
        } catch (Exception) {
            Log::error('🛑 Error running tips');
            Notification::new()
                ->title('🛑 Error')
                ->message('Error running tips')
                ->show();
        }
    }

    private function processTip($tip): true
    {
        return true;
    }
}
