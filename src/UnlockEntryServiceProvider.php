<?php

namespace Emobility\UnlockEntry;

use Illuminate\Support\ServiceProvider;

class UnlockEntryServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(\Illuminate\Routing\Router $router)
    {
        include __DIR__.'/routes.php';
        $this->loadViewsFrom(__DIR__ . '/Views', 'unlock-entry-views');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->make('Emobility\UnlockEntry\Controllers\UnlockEntryController');
    }
}
