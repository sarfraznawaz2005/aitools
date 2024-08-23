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
        $tips = Tip::all();

        foreach ($tips as $tip) {
            if ($tip->active) {

                $schedule->call(fn() => $this->processTip($tip))
                    ->name($tip->name)
                    ->withoutOverlapping()
                    ->timezone('Asia/Karachi')
                    ->cron($tip->cron)
                    ->onSuccess(function () use ($tip) {
                        Notification::new()
                            ->title('✅ ' . $tip->name)
                            ->message("[{$tip->name}] ran successfully.")
                            ->show();
                    })
                    ->onFailure(function () use ($tip) {
                        Notification::new()
                            ->title('🛑 ' . $tip->name)
                            ->message("[{$tip->name}] failed to run.")
                            ->show();
                    });
            }
        }
    }

    private function processTip($tip): true
    {
        return true;
    }
}
