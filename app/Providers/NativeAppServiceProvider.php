<?php

namespace App\Providers;

use Native\Laravel\Contracts\ProvidesPhpIni;
use Native\Laravel\Facades\MenuBar;
use Native\Laravel\Menu\Menu;

class NativeAppServiceProvider implements ProvidesPhpIni
{
    /**
     * Executed once the native application has been booted.
     * Use this method to open windows, register global shortcuts, etc.
     */
    public function boot(): void
    {
        Menu::new()->register();

        MenuBar::create()
            ->icon(public_path('assets/icon.png'))
            ->label('AiTools')
            //->onlyShowContextMenu()
            ->showDockIcon()
            ->route('home')
            ->withContextMenu(
                Menu::new()->quit()
            );

        /*
        GlobalShortcut::new()
            ->key('CmdOrCtrl+Shift+I')
            //->event(ShortcutPressed::class)
            ->register();
        */

        openWindow('main', 'home');
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
