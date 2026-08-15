<?php

use Dotburo\Molog\MologServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

uses(RefreshDatabase::class);

it('merges the package configuration', function () {
    expect(config('molog.gauge_float_round'))->toBe(-1);
    expect(config('molog.per_page'))->toBe(10);
    expect(config('molog.primary_key_type'))->toBe('int');
});

it('creates the tables through the package migration', function () {
    expect(Schema::hasTable('messages'))->toBeTrue();
    expect(Schema::hasTable('gauges'))->toBeTrue();
});

it('publishes the configuration file', function () {
    $paths = ServiceProvider::pathsToPublish(MologServiceProvider::class, 'laravel-molog-config');

    expect($paths)->toHaveCount(1);
    expect(array_key_first($paths))->toBeReadableFile();
    expect(reset($paths))->toEndWith('molog.php');
});

it('publishes the migration file', function () {
    $paths = ServiceProvider::pathsToPublish(MologServiceProvider::class, 'laravel-molog-migrate');

    expect($paths)->toHaveCount(1);
    expect(array_key_first($paths))->toBeReadableFile();
});

it('returns an anonymous migration instance', function () {
    $migration = require __DIR__ . '/../database/migrations/2021_10_14_000000_create_molog_tables.php';

    expect($migration)->toBeInstanceOf(Illuminate\Database\Migrations\Migration::class);
});
