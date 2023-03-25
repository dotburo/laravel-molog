<?php

use Dotburo\Molog\Factories\MessageFactory;
use Dotburo\Molog\Constants;
use Dotburo\Molog\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can create, add and update messages', function () {
    $msgFactory = new MessageFactory();
    $msgFactory->add('Test process started', Constants::NOTICE);
    $msgFactory->notice('Test process continued');
    $msgFactory->setTenant(5);
    $msgFactory->setContext('testing');

    expect($msgFactory->count())->toBe(2);
    expect($msgFactory->last()->body)->toBe('Test process continued');

    /** @var Message $lastMessage */
    $lastMessage = $msgFactory->last();
    $lastUuid = $msgFactory->previousUuid();

    expect($lastUuid)->toMatch('#^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$#i');

    $lastMessage->body = 'Test process initiated';
    $lastMessage->level = Constants::DEBUG;

    expect($msgFactory->last()->body)->toBe('Test process initiated');
    expect($msgFactory->last()->level)->toBe(Constants::DEBUG);

    $msgFactory->setBody('Test process begun');
    $lastMessage->level = Constants::INFO;

    expect($msgFactory->last()->body)->toBe('Test process begun');
    expect($msgFactory->last()->level)->toBe(Constants::INFO);

    expect($msgFactory->last()->tenant_id)->toBe(5);

    expect($msgFactory->count())->toBe(2);

    /*
    expect((string)$msgFactory)->toBe(
        "[notice] Test process started" . PHP_EOL
        . '[info] testing: Test process begun'
    );*/
});
