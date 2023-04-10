<?php

use Dotburo\Molog\Models\Gauge;
use Dotburo\Molog\Models\Message;
use Dotburo\Molog\MologConstants;
use Dotburo\Molog\Traits\Logging;
use Illuminate\Foundation\Auth\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Psr\Log\LogLevel;

uses(RefreshDatabase::class);
uses(Logging::class);

it('creates & stores one message', function () {
    # Message with all possible defaults.
    $this->message('HELLO')->save();

    /** @var Message $message */
    $message = Message::query()->first();

    expect($message->subject)->toBe('HELLO');
    expect($message->level)->toBe(MologConstants::MSG_DEFAULT_LEVEL);
    expect($message->body)->toBeNull();
    expect($message->context)->toBeNull();
    expect($message->user_id)->toBeNull();
    expect($message->tenant_id)->toBeNull();

    $dbDatetimeFormatRegex = '/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\.\d{3}$/';

    expect($message->getRawOriginal('created_at'))->toMatch($dbDatetimeFormatRegex);
});

it('sets created_at upon instantiation', function () {
    $msg = $this->message()->log('HELLO');

    $serialisedMsg = $msg->toArray();

    $msg->save();

    /** @var Message $storedMsg */
    $storedMsg = Message::query()->first();

    expect($storedMsg->getRawOriginal('created_at'))->toBe($serialisedMsg['created_at']);
});

it('overwrites itself', function () {
    /** @var Message $msg */
    $msg = $this->message($subject = 'Process started...');

    expect($msg->subject)->toBe($subject);
    expect($msg->level)->toBe(MologConstants::MSG_DEFAULT_LEVEL);

    $msg->log($subject = 'Process running...', LogLevel::INFO);

    expect($msg->subject)->toBe($subject);
    expect($msg->level)->toBe(LogLevel::INFO);

    $msg->notice($subject = 'Process stalling...');

    expect($msg->subject)->toBe($subject);
    expect($msg->level)->toBe(LogLevel::NOTICE);

    $msg->info($subject = 'Process ETA 1h...');

    expect($msg->subject)->toBe($subject);
    expect($msg->level)->toBe(LogLevel::INFO);

    $msg->error($subject = 'Process failed...');

    $msg->save();

    /** @var Message $storedMsg */
    $storedMsg = Message::query()->first();

    expect($storedMsg->subject)->toBe($subject);
    expect($storedMsg->level)->toBe(LogLevel::ERROR);
});

it('breaks down exceptions', function () {
    $exception = new Exception('Something went wrong');

    $this->message($exception)->save();

    /** @var Message $msg */
    $msg = Message::query()->first();

    expect($msg->level)->toBe(MologConstants::MSG_DEFAULT_LEVEL);
    expect($msg->subject)->toBe($exception->getMessage());
    expect($msg->body)->toBe($exception->getTraceAsString());
});

it('sets & stores all attributes through setter methods', function () {
    $this->message()
        ->setSubject($subject = 'Testing attribute setters')
        ->setLevel(LogLevel::CRITICAL)
        ->setContext($context = 'testing')
        ->setBody($body = '...')
        ->setTenant($tenant = 3)
        ->setUser($user = 2)
        ->save();

    /** @var Message $msg */
    $msg = Message::query()->first();

    expect($msg->subject)->toBe($subject);
    expect($msg->level)->toBe(LogLevel::CRITICAL);
    expect($msg->body)->toBe($body);
    expect($msg->context)->toBe($context);
    expect($msg->user_id)->toBe($user);
    expect($msg->tenant_id)->toBe($tenant);
});

it('sets & stores global attributes', function () {
    $this->messageFactory()
        ->setContext($context = 'testing')
        ->setTenant($tenant = 3)
        ->setUser($user = 2);

    $this->message()->critical($subject = 'Testing global attribute')->save();

    /** @var Message $msg */
    $msg = Message::query()->first();

    expect($msg->subject)->toBe($subject);
    expect($msg->level)->toBe(LogLevel::CRITICAL);
    expect($msg->context)->toBe($context);
    expect($msg->user_id)->toBe($user);
    expect($msg->tenant_id)->toBe($tenant);
});

it('allows models in user and tenant setters', function () {
    $user1 = new User();
    $user1->id = 123;

    $user2 = new User();
    $user2->id = 234;

    $this->messageFactory()
        ->setTenant($user1)
        ->setUser($user1);

    $this->message()
        ->emergency($subject = 'Testing model arguments')
        ->setTenant($user2)
        ->setUser($user2)
        ->save();

    /** @var Message $msg */
    $msg = Message::query()->first();

    expect($msg->subject)->toBe($subject);
    expect($msg->level)->toBe(LogLevel::EMERGENCY);
    expect($msg->user_id)->toBe($user2->id);
    expect($msg->tenant_id)->toBe($user2->id);
});

it('instantiates and stores multiple messages', function () {
    $this->messageFactory()
        ->setContext($context = 'testing')
        ->emergency($subject1 = 'Testing model arguments 1')
        ->notice($subject2 = 'Testing model arguments 2')
        ->save();

    /** @var Message $msg1 */
    $msg1 = Message::query()->level(LogLevel::EMERGENCY)->first();

    /** @var Message $firstMsg */
    $msg2 = Message::query()->level(LogLevel::NOTICE)->first();

    expect($msg1->subject)->toBe($subject1);
    expect($msg1->level)->toBe(LogLevel::EMERGENCY);
    expect($msg1->context)->toBe($context);

    expect($msg2->subject)->toBe($subject2);
    expect($msg2->level)->toBe(LogLevel::NOTICE);
    expect($msg2->context)->toBe($context);
});

it('associates parent models', function () {
    $user = new User();
    $user->id = 123;

    $this->message('Sending email...', $level = LogLevel::INFO)
        ->concerning($user)
        ->save();

    $this->gauge('duration', 120, 's', 'int')
        ->concerning($this->messageFactory()->last())
        ->save();

    /** @var Message $message */
    $message = Message::query()->first();

    /** @var Gauge $gauge */
    $gauge = $message->gauges()->first();

    expect($message->level)->toBe($level);
    expect($message->loggable_type)->toBe('Illuminate\Foundation\Auth\User');
    // the following is manually type-casted because it gives different data types in different Laravel versions
    expect((int)$message->loggable_id)->toBe($user->id);

    expect($gauge->key)->toBe('duration');
    expect($gauge->value)->toBe(120);
    expect($gauge->type)->toBe('int');
    expect($gauge->unit)->toBe('s');
});

it('nicely outputs to strings', function () {
    $this->message('Mail sent', LogLevel::NOTICE)
        ->setContext('testing')
        ->save();

    $this->gauge('duration', 120.3, 's')
        ->concerning($this->messageFactory()->last())
        ->setContext('testing')
        ->save();

    /** @var Message $message */
    $message = Message::query()->first();

    /** @var Gauge $gauge */
    $gauge = $message->gauges()->first();

    $dt = $message->created_at->toDateTimeString('millisecond');

    expect((string)$message)->toBe("$dt [notice] [testing] Mail sent");

    expect((string)$gauge)->toBe('[testing] duration: 120.3s');
});
