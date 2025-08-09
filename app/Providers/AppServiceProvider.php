<?php

namespace App\Providers;

use Exception;
use Illuminate\Console\Events\CommandStarting;
use Illuminate\Support\Facades\Event;
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

        config(['app.timezone' => System::timezone() ?? 'Asia/Karachi']); // via nativephp

        $this->registerCustomValidators();

        // nativephp seems to copy php 8.4, so we are going to instead copy 8.3
        // to "D:\laragon\www\aitools\vendor\nativephp\php-bin\bin\win\x64"
        /*
        Event::listen(CommandStarting::class, function (CommandStarting $event) {
            if ($event->command !== 'native:serve') {
                return;
            }

            // Only do this on Windows; adjust if you support mac/linux too
            if (stripos(PHP_OS_FAMILY, 'Windows') === false) {
                return;
            }

            $source = base_path('php-8.3.zip');
            $dest = base_path('vendor/nativephp/php-bin/bin/win/x64/php-8.3.zip');

            if (!is_file($source)) {
                echo "[copy-php] Source missing: {$source}\n";
                return;
            }

            // Ensure destination directory exists
            $destDir = dirname($dest);
            if (!is_dir($destDir)) {
                @mkdir($destDir, 0777, true);
            }

            // Skip if sizes match
            $doCopy = true;
            if (is_file($dest) && filesize($source) === filesize($dest)) {
                $doCopy = false;
            }

            if ($doCopy) {
                if (!@copy($source, $dest)) {
                    $err = error_get_last();
                    echo "[copy-php] Failed to copy: {$err['message']}\n";
                    return;
                }
                echo "[copy-php] Copied {$source} → {$dest}\n";
            } else {
                echo "[copy-php] Already up to date; skip\n";
            }
        });
        */
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
