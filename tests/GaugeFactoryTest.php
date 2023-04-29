<?php

use Dotburo\Molog\Exceptions\MologException;
use Dotburo\Molog\Factories\GaugeFactory;
use Dotburo\Molog\Models\Gauge;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('starts and stops timers', function () {
    $factory = new GaugeFactory();

    $factory->startTimer();
    $factory->stopTimer();

    $factory->startTimer('time');
    $factory->stopTimer('time');

    expect($factory->get('duration')->value)->toBeFloat();
    expect($factory->get('time')->value)->toBeFloat();
});

it('cannot stop non-existent timer', function () {
    $factory = new GaugeFactory();

    $factory->stopTimer();

})->expectException(MologException::class);

it('increments and decrements gauges', function () {
    $factory = new GaugeFactory();

    $factory->increment('test');
    $factory->increment('test');
    $factory->increment('test');
    $factory->decrement('test');

    expect($factory->last()->value)->toBe(2);
});

it('sets an initially decremented gauge at -1', function () {
    $factory = new GaugeFactory();

    $factory->decrement('test');

    expect($factory->last()->value)->toBe(-1);
});

it('creates and updates gauge attributes', function () {
    $gaugeFactory = new GaugeFactory();

    $gaugeFactory->setTenant(5);

    $gaugeFactory->gauge('pressure', 2.35, 'bar');

    $gaugeFactory->setContext('Import process');

    $gaugeFactory->gauge('density', 5.43);

    $gaugeFactory->last()->setValue(5)->setTenant(7)->setContext();

    expect($gaugeFactory->count())->toBe(2);
    expect($gaugeFactory->last()->key)->toBe('density');

    /** @var Gauge $lastMetric */
    $lastMetric = $gaugeFactory->last();

    expect($gaugeFactory->last()->value)->toBe(5);

    $lastMetric->value = 3.35;

    expect($gaugeFactory->last()->value)->toBe(3.35);

    expect($gaugeFactory->first()->tenant_id)->toBe(5);
    expect($gaugeFactory->last()->tenant_id)->toBe(7);

    expect($gaugeFactory->last()->context)->toBeNull();
    expect($gaugeFactory->first()->context)->toBe('Import process');

    expect($gaugeFactory->count())->toBe(2);

    expect((string)$gaugeFactory)->toBe("density: 3.35" . PHP_EOL . "[Import process] pressure: 2.35bar");
});
