<?php

use Dotburo\Molog\Models\Message;
use Dotburo\Molog\MologConstants;
use Dotburo\Molog\Tests\Logs;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can create, add and update messages', function () {
    $logging = new Logs();

    $logging->handle();

    /** @var Message $saved */
    $saved = Message::query()->first();

    expect($saved->level)->toBe(MologConstants::ERROR);
    expect($saved->loggable_type)->toBe('Illuminate\Foundation\Auth\User');
    expect((int)$saved->loggable_id)->toBe(4); // this manually type casted because it gives different data types in different Laravel versions
    expect($saved->context)->toBe('mailing');
});
