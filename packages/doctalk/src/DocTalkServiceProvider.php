<?php

namespace Package\DocTalk;

use Illuminate\Support\ServiceProvider;

class DocTalkServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (!config('doctalk.enabled')) {
            return;
        }

        // routes
        if (!$this->app->routesAreCached()) {
            require __DIR__ . '/Http/routes.php';
        }

        // views
        $this->loadViewsFrom(__DIR__ . '/Views', 'doctalk');

        // publish our files over to main laravel app
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/Assets' => public_path('vendor/doctalk/assets'),
                __DIR__ . '/Config/doctalk.php' => config_path('doctalk.php'),
                __DIR__ . '/Migrations' => database_path('migrations')
            ]);
        }
    }

    public function register()
    {
        //
    }
}
