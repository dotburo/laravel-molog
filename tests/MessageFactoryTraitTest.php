<?php

use Dotburo\LogMetrics\Factories\MessageFactory;
use Dotburo\LogMetrics\LogMetricsConstants;
use Dotburo\LogMetrics\Models\Message;
use Dotburo\LogMetrics\Tests\Logs;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can create, add and update messages', function () {
    $logging = new Logs();
    $logging->handle();

    /** @var Message $saved */
    $saved = Message::query()->first();

    expect($saved->level)->toBe(LogMetricsConstants::ERROR);
    expect($saved->loggable_type)->toBe('App\Models\User');
    expect($saved->loggable_id)->toBe('4');
    expect($saved->context)->toBe('App\Mail\Mailable');
});
