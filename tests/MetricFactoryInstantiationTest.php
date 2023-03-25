<?php

use Dotburo\Molog\Factories\GaugeFactory;
use Dotburo\Molog\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can create, add and update gauges', function () {
    $gaugeFactory = new GaugeFactory();
    $gaugeFactory->setTenantGlobally(5);
    $gaugeFactory->add('pressure', 2.35, 'int', 'bar');
    $gaugeFactory->add('density', 5.43);
    $gaugeFactory->setContext('Import process');
    //$gaugeFactory->setRelation($messageFactory->last());
    $gaugeFactory->last()->value = 5;
    //$gaugeFactory->save();
    //dd($gaugeFactory->toArray());
    expect($gaugeFactory->count())->toBe(2);
    expect($gaugeFactory->last()->key)->toBe('density');

    /** @var Message $lastMetric */
    $lastMetric = $gaugeFactory->last();
    $lastUuid = $gaugeFactory->previousUuid();

    expect($lastUuid)->toMatch('#^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$#i');
    expect($gaugeFactory->last()->value)->toBe(5.0);


    $gaugeFactory->setKey('density 2');
    $gaugeFactory->setType('int');
    $gaugeFactory->setValue(6);
    $gaugeFactory->setTenant(7);

    expect($gaugeFactory->last()->value)->toBe(6);

    $lastMetric->type = 'float';
    $lastMetric->value = 3.35;

    expect($gaugeFactory->last()->value)->toBe(3.35);


    expect($gaugeFactory->last()->key)->toBe('density 2');

    expect($gaugeFactory->first()->tenant_id)->toBe(5);
    expect($gaugeFactory->last()->tenant_id)->toBe(7);

    expect($gaugeFactory->first()->context)->toBeNull();
    expect($gaugeFactory->last()->context)->toBe('Import process');

    expect($gaugeFactory->count())->toBe(2);

    expect((string)$gaugeFactory)->toBe("→ Import process: density 2: 3.35" . PHP_EOL . "→ pressure: 2 bar");
});
