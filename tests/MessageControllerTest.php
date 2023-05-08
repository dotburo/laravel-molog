<?php

use Dotburo\Molog\Traits\Logging;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;

uses(RefreshDatabase::class);
uses(Logging::class);

it('paginates HTTP query results', function () {
    $this->messages()
        ->info('HELLO')
        ->warning('BYE')
        ->save();

    $this->gauges()
        ->gauge('divided', 33)
        ->gauge('divider', 100)
        ->percentage('percentage', 'divided', 'divider')
        ->concerning($this->messages()->last())
        ->save();

    /** @var TestResponse $response */
    $response = $this->get('/messages?levels=info,warning&order_by=subject&order=asc&per_page=2');

    $responseData = $response->json();

    expect($responseData['data'])->toBeArray();
    expect($responseData['per_page'])->toBe(2);
    expect($responseData['total'])->toBe(2);
    expect($responseData['data'][0]['subject'])->toBe('BYE');
    expect(count($responseData['data'][0]['gauges']))->toBe(3);
});
