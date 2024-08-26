<?php

namespace App\Providers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Native\Laravel\Contracts\ProvidesPhpIni;
use Native\Laravel\Facades\MenuBar;
use Native\Laravel\Facades\Settings;
use Native\Laravel\Menu\Menu;

class NativeAppServiceProvider implements ProvidesPhpIni
{
    public function boot(): void
    {
        // NOTE: see config.app app_url, it is set to nativephp default

        if (!Schema::hasTable('bots')) {
            Artisan::call('native:db:seed');
        }

        $alwaysOnTop = Settings::get('settings.alwaysOnTop', false);
        $page = Settings::get('settings.page', 'home');
        $width = Settings::get('settings.width', 1280);
        $height = Settings::get('settings.height', 800);

        // remove default menu
        Menu::new()->register();

        MenuBar::create()
            //->label(config('app.name'))
            ->icon(public_path('assets/menuBarIconTemplate.png'))
            ->showDockIcon(false)
            ->alwaysOnTop((bool)$alwaysOnTop)
            ->route($page)
            ->width($width)
            ->maxWidth($width)
            ->minWidth(300)
            ->height($height)
            ->maxHeight($height)
            ->minHeight(400);

        //openWindow('main', 'home');
    }

    /**
     * Return an array of php.ini directives to be set.
     */
    public function phpIni(): array
    {
        return [
            'memory_limit' => '2G',
            'display_errors' => '1',
            'error_reporting' => 'E_ALL',
            'max_execution_time' => '0',
            'max_input_time' => '0',
        ];
    }
}
