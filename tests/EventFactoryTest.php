<?php

use Dotburo\Molog\Exceptions\MologException;
use Dotburo\Molog\Factories\GaugeFactory;
use Dotburo\Molog\Models\Gauge;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;

uses(RefreshDatabase::class);

it('returns a collection of events', function () {
    $factory = new GaugeFactory();

    expect($factory->collection())->toBeInstanceOf(Collection::class);
});

it('throws an exception for non-existent collection methods', function () {
    $factory = new GaugeFactory();

    $factory->sdfjmk();
})->expectException(MologException::class);
