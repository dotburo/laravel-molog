<?php

use Dotburo\LogMetrics\Factories\MetricFactory;
use Dotburo\LogMetrics\Models\Metric;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can test', function () {
    $metricFactory = new MetricFactory();
    $metricFactory->add('pressure', 2.35, 'bar', 'int');
    $metricFactory->add('density', 5.43);
    $metricFactory->setTenant(5);
    $metricFactory->setContext('Import process');
    //$metricFactory->setRelation($messageFactory->last());
    $metricFactory->last()->value = 5.45;
    //$metricFactory->save();

    /** @var Metric $lastMetric */
    $lastMetric = $metricFactory->last();

    expect($lastMetric->value)->toBe(5.45);
    expect($lastMetric->tenant_id)->toBe(5);
    expect($lastMetric->key)->toBe('density');
    expect($lastMetric->context)->toBe('Import process');

});
