<?php

use Dotburo\Molog\Factories\GaugeFactory;
use Dotburo\Molog\Models\Gauge;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can create, add and update gauges', function () {
    $gaugeFactory = new GaugeFactory();
    $gaugeFactory->setTenant(5);
    $gaugeFactory->gauge('pressure', 2.35, 'bar');
    $gaugeFactory->setTenant(7);
    $gaugeFactory->setContext('Import process');
    $gaugeFactory->gauge('density', 5.43);
    //$gaugeFactory->concerning($messageFactory->last());
    $gaugeFactory->last()->setValue(5);
    //$gaugeFactory->save();
    //dd($gaugeFactory->toArray());
    expect($gaugeFactory->count())->toBe(2);
    expect($gaugeFactory->last()->key)->toBe('density');

    /** @var Gauge $lastMetric */
    $lastMetric = $gaugeFactory->last();

    expect($gaugeFactory->last()->value)->toBe(5);

    $lastMetric->value = 3.35;

    expect($gaugeFactory->last()->value)->toBe(3.35);

    expect($gaugeFactory->first()->tenant_id)->toBe(5);
    expect($gaugeFactory->last()->tenant_id)->toBe(7);

    expect($gaugeFactory->first()->context)->toBeNull();
    expect($gaugeFactory->last()->context)->toBe('Import process');

    expect($gaugeFactory->count())->toBe(2);

    expect((string)$gaugeFactory)->toBe("[Import process] density: 3.35" . PHP_EOL . "pressure: 2.35bar");
});
