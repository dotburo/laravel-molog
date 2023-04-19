<?php

use Dotburo\Molog\Factories\MessageFactory;
use Dotburo\Molog\Models\Message;
use Dotburo\Molog\MologConstants;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can create, add and update messages', function () {
    $msgFactory = new MessageFactory();
    $msgFactory->setTenant(5);
    $msgFactory->setContext('testing');
    $msgFactory->message('Test process started', MologConstants::NOTICE);
    $msgFactory->log(MologConstants::NOTICE, 'Test process started');
    $msgFactory->last()->notice('Test process continued');

    expect($msgFactory->count())->toBe(2);
    expect($msgFactory->last()->subject)->toBe('Test process continued');

    /** @var Message $lastMessage */
    $lastMessage = $msgFactory->last();

    $lastMessage->subject = 'Test process initiated';
    $lastMessage->level = MologConstants::DEBUG;

    expect($msgFactory->last()->subject)->toBe('Test process initiated');
    expect($msgFactory->last()->level)->toBe(MologConstants::DEBUG);

    $lastMessage->setBody('Test process begun');
    $lastMessage->level = MologConstants::INFO;

    expect($msgFactory->last()->body)->toBe('Test process begun');
    expect($msgFactory->last()->level)->toBe(MologConstants::INFO);

    expect($msgFactory->last()->tenant_id)->toBe(5);

    expect($msgFactory->count())->toBe(2);

    /*
    expect((string)$msgFactory)->toBe(
        "[notice] Test process started" . PHP_EOL
        . '[info] testing: Test process begun'
    );*/
});
