<?php

use Dotburo\LogMetrics\Factories\MetricFactory;
use Dotburo\LogMetrics\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can create, add and update metrics', function () {
    $metricFactory = new MetricFactory();
    $metricFactory->setTenantGlobally(5);
    $metricFactory->add('pressure', 2.35, 'int', 'bar');
    $metricFactory->add('density', 5.43);
    $metricFactory->setContext('Import process');
    //$metricFactory->setRelation($messageFactory->last());
    $metricFactory->last()->value = 5;
    //$metricFactory->save();
//dd($metricFactory->toArray());
    expect($metricFactory->count())->toBe(2);
    expect($metricFactory->last()->key)->toBe('density');

    /** @var Message $lastMetric */
    $lastMetric = $metricFactory->last();
    $lastUuid = $metricFactory->previousUuid();

    expect($lastUuid)->toMatch('#^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$#i');
    expect($metricFactory->last()->value)->toBe(5.0);


    $metricFactory->setKey('density 2');
    $metricFactory->setType('int');
    $metricFactory->setValue(6);
    $metricFactory->setTenant(7);

    expect($metricFactory->last()->value)->toBe(6);

    $lastMetric->type = 'float';
    $lastMetric->value = 3.35;

    expect($metricFactory->last()->value)->toBe(3.35);


    expect($metricFactory->last()->key)->toBe('density 2');

    expect($metricFactory->first()->tenant_id)->toBe(5);
    expect($metricFactory->last()->tenant_id)->toBe(7);

    expect($metricFactory->first()->context)->toBeNull();
    expect($metricFactory->last()->context)->toBe('Import process');

    expect($metricFactory->count())->toBe(2);

    expect((string)$metricFactory)->toBe("→ Import process: density 2: 3.35" . PHP_EOL . "→ pressure: 2 bar");
});
