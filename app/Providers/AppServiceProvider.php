<?php

namespace App\Providers;

use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Lorisleiva\CronTranslator\CronTranslator;
use Native\Laravel\Facades\System;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // now doing directly from migration instead: database/migrations/2024_08_15_074354_create_bots_table.php:27
        //Artisan::call('native:db:seed --force');

        config(['app.timezone' => System::timezone()]); // via nativephp

        $this->registerCustomValidators();
    }

    /**
     * @return void
     */
    public function registerCustomValidators(): void
    {
        Validator::extend('max_combined_size', function ($attribute, $value, $parameters) {
            $maxSize = (int)$parameters[0] * 1024; // Convert to bytes

            $totalSize = array_reduce($value, function ($carry, $file) {
                return $carry + $file->getSize();
            }, 0);

            return $totalSize <= $maxSize;
        });

        Validator::extend('valid_cron', function ($attribute, $value, $parameters) {
            try {
                CronTranslator::translate(trim($value));
                return true;
            } catch (Exception) {
                return false;
            }
        });
    }
}
