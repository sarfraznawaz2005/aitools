<?php

namespace App\Providers;

use App\Events\QuickChatClicked;
use Illuminate\Support\Facades\Event;
use Native\Laravel\Contracts\ProvidesPhpIni;
use Native\Laravel\Events\App\ApplicationBooted;
use Native\Laravel\Facades\MenuBar;
use Native\Laravel\Facades\Settings;
use Native\Laravel\Facades\Window;
use Native\Laravel\Menu\Menu;

class NativeAppServiceProvider implements ProvidesPhpIni
{
    public function boot(): void
    {
        // NOTE: see config.app app_url, it is set to nativephp default

        $alwaysOnTop = Settings::get('settings.alwaysOnTop', false);
        $page = Settings::get('settings.page', 'home');
        $width = Settings::get('settings.width', 1250);
        $height = Settings::get('settings.height', 750);

        // remove default menu
        Menu::new()->register();

        MenuBar::create()
            ->label(config('app.name'))
            ->tooltip(config('app.name'))
            ->icon(public_path('assets/menuBarIconTemplate.png'))
            ->showDockIcon(false)
            ->alwaysOnTop((bool)$alwaysOnTop)
            ->route($page)
            ->width($width)
            ->maxWidth($width)
            ->minWidth(300)
            ->height($height)
            ->maxHeight($height)
            ->minHeight(400)
            ->withContextMenu(
                Menu::new()
                    ->event(QuickChatClicked::class, 'Quick Chat')
                    ->separator()
                    ->quit()
            );

        // fix for stoping app from closing when quick chat window is opened first and closed
        Event::listen(ApplicationBooted::class, function () {
            Window::open()
                ->showDevTools(false)
                ->transparent()
                ->invisibleFrameless()
                ->position(-100000, -100000)
                ->width(0)
                ->height(0);
        });
    }

    /**
     * Return an array of php.ini directives to be set.
     */
    public function phpIni(): array
    {
        return [
            'memory_limit' => '4G',
            'display_errors' => '1',
            'error_reporting' => 'E_ALL',
            'max_execution_time' => '0',
            'max_input_time' => '0',
            'upload_max_filesize' => '50M',
            'post_max_size' => '50M',
        ];
    }
}
