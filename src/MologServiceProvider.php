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
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrations();

            $this->publishResources();
        }
    }

    /** @inheritdoc */
    public function register(): void
    {
        $this->mergeConfigFrom($this->configPath(), 'molog');
    }

    /**
     * Copies files to parent project.
     * @return void
     */
    protected function publishResources(): void
    {
        $this->publishes([
            $this->migrationPath() => database_path('migrations'),
        ], 'laravel-molog-migrate');

        $this->publishes([
            $this->configPath() => config_path('molog.php'),
        ], 'laravel-molog-config');
    }

    /**
     * Register the package's migration files.
     * @return void
     */
    protected function loadMigrations(): void
    {
        $this->loadMigrationsFrom($this->migrationPath());
    }

    /**
     * Absolute path of the package's configuration file.
     * @return string
     */
    protected function configPath(): string
    {
        return __DIR__ . '/../config/molog.php';
    }

    /**
     * Absolute path of the package's migration file.
     * @return string
     */
    protected function migrationPath(): string
    {
        return __DIR__ . '/../database/migrations/2021_10_14_000000_create_molog_tables.php';
    }
}
