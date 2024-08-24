<?php

namespace App\Providers;

use Native\Laravel\Contracts\ProvidesPhpIni;
use Native\Laravel\Facades\MenuBar;
use Native\Laravel\Facades\Window;
use Native\Laravel\Menu\Items\MenuItem;
use Native\Laravel\Menu\Menu;

class NativeAppServiceProvider implements ProvidesPhpIni
{
    /**
     * Executed once the native application has been booted.
     * Use this method to open windows, register global shortcuts, etc.
     */
    public function boot(): void
    {
        //MenuBar::hide();

        Menu::new()->register();

        Window::open()
            //->showDevTools(false)
            //->frameless()
            //->titleBarHidden()
            //->fullscreen(true)
            ->width(1280)
            ->minWidth(1024)
            ->height(800)
            ->minHeight(800)
            ->focusable()
            ->hasShadow()
            ->lightVibrancy()
            ->rememberState()
            ->maximizable();

        MenuBar::create()
            ->icon(public_path('assets/icon.png'))
            ->label(config('app.name'))
            ->showDockIcon()
            ->withContextMenu(
                Menu::new()
                    ->event(MenuItem::class, 'About')
                    ->quit()
                    ->link(route('home'), 'Home')
            );

        /*
        GlobalShortcut::new()
            ->key('CmdOrCtrl+Shift+I')
            //->event(ShortcutPressed::class)
            ->register();
        */

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
