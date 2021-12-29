<?php

use Dotburo\LogMetrics\Factories\MetricFactory;
use Dotburo\LogMetrics\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can create, add and update metrics', function () {
    $metricFactory = new MetricFactory();
    $metricFactory->add('pressure', 2.35, 'int', 'bar');
    $metricFactory->add('density', 5.43);
    $metricFactory->setTenant(5);
    $metricFactory->setContext('Import process');
    //$metricFactory->setRelation($messageFactory->last());
    $metricFactory->last()->value = 5;
    //$metricFactory->save();

    expect($metricFactory->count())->toBe(2);
    expect($metricFactory->last()->key)->toBe('density');

    /** @var Message $lastMetric */
    $lastMetric = $metricFactory->last();
    $lastUuid = $lastMetric->getKey();

    expect($lastUuid)->toMatch('#^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$#i');
    expect($metricFactory->last()->value)->toBe(5.0);


    $metricFactory->setKey($lastUuid, 'density 2');
    $metricFactory->setType($lastUuid, 'int');
    $metricFactory->setValue($lastUuid, 6);

    expect($metricFactory->last()->value)->toBe(6);

    $lastMetric->type = 'float';
    $lastMetric->value = 3.35;

    expect($metricFactory->last()->value)->toBe(3.35);

    expect($metricFactory->last()->tenant_id)->toBe(5);
    expect($metricFactory->last()->key)->toBe('density 2');
    expect($metricFactory->last()->context)->toBe('Import process');

    expect($metricFactory->count())->toBe(2);

    expect((string)$metricFactory)->toBe("→ pressure: 2 bar" . PHP_EOL . "→ density 2: 3.35");
});
