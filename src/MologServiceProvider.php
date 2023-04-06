<?php

namespace Dotburo\Molog;

use Illuminate\Support\ServiceProvider;

/**
 * Setup the package.
 *
 * @copyright 2021 dotburo
 * @author dotburo <code@dotburo.org>
 */
class MologServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrations();

            $this->publishResources();
        }
    }

    /** @inheritdoc */
    public function register()
    {
        $this->mergeConfigFrom(realpath(__DIR__ . '/../config/molog.php'), 'molog');
    }

    /**
     * Copies files to parent project.
     * @return void
     */
    protected function publishResources(): void
    {
        $this->publishes([
            realpath(__DIR__ . '/../database/migrations/2021_10_14_000000_create_molog_tables.php') => database_path('migrations'),
        ], 'laravel-molog-migrate');

        $this->publishes([
            realpath(__DIR__ . '/../config/molog.php') => config_path('molog.php'),
        ], 'laravel-molog-config');
    }

    /**
     * Register Stargate's migration files.
     * @return void
     */
    protected function loadMigrations(): void
    {
        $this->loadMigrationsFrom(
            realpath(__DIR__ . '/../database/migrations/2021_10_14_000000_create_molog_tables.php')
        );
    }
}
