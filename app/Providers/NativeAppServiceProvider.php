<?php

namespace App\Providers;

use Native\Laravel\Contracts\ProvidesPhpIni;
use Native\Laravel\Facades\Window;
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
            ->width(1080)
            ->minWidth(1080)
            ->maxWidth(1080)
            ->height(800)
            ->minHeight(800)
            //->showDevTools(false)
            ->maximizable(false);

        /**
         * Dock::menu(
         * Menu::new()
         * ->event(DockItemClicked::class, 'Settings')
         * ->submenu('Help',
         * Menu::new()
         * ->event(DockItemClicked::class, 'About')
         * ->event(DockItemClicked::class, 'Learn Moreā¦')
         * )
         * );
         *
         * ContextMenu::register(
         * Menu::new()
         * ->event(ContextMenuClicked::class, 'Do something')
         * );
         *
         * GlobalShortcut::new()
         * ->key('CmdOrCtrl+Shift+I')
         * ->event(ShortcutPressed::class)
         * ->register();
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
