<?php

namespace dotburo\LogMetrics;

use Illuminate\Support\ServiceProvider;

/**
 * Setup the package.
 *
 * @copyright 2021 dotburo
 * @author dotburo <code@dotburo.org>
 */
class LogMetricsServiceProvider extends ServiceProvider
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

    /** @inheritDoc */
    public function register()
    {
        $this->mergeConfigFrom(realpath(__DIR__ . '/../config/log-metrics.php'), 'log-metrics');
    }

    /**
     * Copies files to parent project.
     * @return void
     */
    protected function publishResources(): void
    {
        $this->publishes([
            realpath(__DIR__ . '/../database/migrations/2021_10_14_000000_create_log_metrics_tables.php') => database_path('migrations'),
        ], 'laravel-log-metrics-migrate');

        $this->publishes([
            realpath(__DIR__ . '/../config/log-metrics.php') => config_path('log-metrics.php'),
        ], 'laravel-log-metrics-config');
    }

    /**
     * Register Stargate's migration files.
     * @return void
     */
    protected function loadMigrations(): void
    {
        $this->loadMigrationsFrom(
            realpath(__DIR__ . '/../database/migrations/2021_10_14_000000_create_log_metrics_tables.php')
        );
    }
}
