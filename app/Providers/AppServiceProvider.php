<?php

namespace App\Providers;

use Closure;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Lorisleiva\CronTranslator\CronTranslator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        //$this->copyNativeAppIcons(); // since it does not have a way to change icon currently

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

    private function copyNativeAppIcons(): void
    {
        copy(public_path('assets/icon.png'), base_path('vendor/nativephp/electron/resources/js/resources/icon.png'));
        copy(public_path('assets/menuBarIconTemplate.png'), base_path('vendor/nativephp/electron/resources/js/resources/menuBarIconTemplate.png'));
        copy(public_path('assets/menuBarIconTemplate@2x.png'), base_path('vendor/nativephp/electron/resources/js/resources/menuBarIconTemplate@2x.png'));
        copy(public_path('assets/menuBarIconTemplate.png'), base_path('vendor/nativephp/electron/resources/js/resources/IconTemplate.png'));
        copy(public_path('assets/menuBarIconTemplate@2x.png'), base_path('vendor/nativephp/electron/resources/js/resources/IconTemplate@2x.png'));
        copy(public_path('assets/icon.png'), base_path('vendor/nativephp/electron/resources/js/build/icon.png'));
    }
}
