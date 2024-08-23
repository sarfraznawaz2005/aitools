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
                    Log::info("Running tip {$tip->id}");
                })->cron($this->getCronExpression($tip));
            }
        } catch (Exception) {

        }
    }

    protected function getCronExpression(Tip $tip)
    {
        if ($tip->schedule_type === 'custom') {
            return $tip->schedule_data['cron'];
        }

        return match ($tip->schedule_type) {
            'every_minute' => '* * * * *',
            'every_hour' => '0 * * * *',
            'every_day' => '0 0 * * *',
            'every_week' => '0 0 * * 0',
            'every_month' => '0 0 1 * *',
        };
    }
}
