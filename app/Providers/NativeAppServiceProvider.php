<?php

namespace App\Providers;

use Native\Laravel\Contracts\ProvidesPhpIni;
use Native\Laravel\Facades\MenuBar;
use Native\Laravel\Menu\Menu;

class NativeAppServiceProvider implements ProvidesPhpIni
{
    public function boot(): void
    {
        // NOTE: see config.app app_url, it is set to nativephp default

        // remove default menu
        Menu::new()->register();

        MenuBar::create()
            //->alwaysOnTop()
            //->label(config('app.name'))
            ->showDockIcon(false)
            ->icon(public_path('assets/menuBarIconTemplate.png'))
            ->width(1250)
            ->height(760);

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
