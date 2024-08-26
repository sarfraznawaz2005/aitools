<?php

namespace App\Providers;

use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
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
        if (!Schema::hasTable('bots')) {
            try {
                Artisan::call('migrate');
                Artisan::call('db:seed');
            } catch (Exception) {
                // Ignore
            }
        }

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
