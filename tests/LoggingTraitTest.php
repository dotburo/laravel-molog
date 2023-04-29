<?php

use Dotburo\Molog\Exceptions\MologException;
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

    //expect($message->getRawOriginal('created_at'))->toMatch($dbDatetimeFormatRegex);
});

it('creates & stores one gauge', function () {
    # Gauge with all possible defaults.
    $this->gauge('metric', 1)->save();

    /** @var Gauge $gauge */
    $gauge = $this->gauges()->last();

    expect($gauge->key)->toBe('metric');
    expect($gauge->value)->toBe(1);
    expect($gauge->unit)->toBeNull();
    expect($gauge->context)->toBeNull();
    expect($gauge->user_id)->toBeNull();
    expect($gauge->tenant_id)->toBeNull();

    $dbDatetimeFormatRegex = '/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\.\d{3}$/';

    //expect($gauge->getRawOriginal('created_at'))->toMatch($dbDatetimeFormatRegex);
});

it('sets created_at upon instantiation', function () {
    $msg = $this->message()->log('HELLO');

    $serialisedMsg = $msg->toArray();

    $msg->save();

    /** @var Message $storedMsg */
    $storedMsg = Message::query()->first();

    expect($storedMsg->getRawOriginal('created_at'))->toBe($serialisedMsg['created_at']);
});

it('cannot save a message without subject', function () {
    $this->message()->save();
})->throws(MologException::class, 'A message without subject cannot be saved');

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

    $this->message()->error($exception)->save();

    /** @var Message $msg */
    $msg = Message::query()->first();

    expect($msg->level)->toBe(MologConstants::ERROR);
    expect($msg->subject)->toBe($exception->getMessage());
    expect($msg->body)->toBe($exception->getTraceAsString());

    $msg = $this->messages()->log($exception)->last();

    expect($msg->level)->toBe(MologConstants::DEBUG);
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
    $this->messages()
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

    $this->messages()
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
    $this->messages()
        ->emergency($subject1 = 'Testing model arguments 1')
        ->debug($subject2 = 'Testing model arguments 2')
        ->setContext($context = 'testing')
        ->save();

    /** @var Message $msg1 */
    $msg1 = Message::query()->level(LogLevel::EMERGENCY)->first();

    /** @var Message $firstMsg */
    $msg2 = Message::query()->level(LogLevel::DEBUG)->first();

    expect($msg1->subject)->toBe($subject1);
    expect($msg1->level)->toBe(LogLevel::EMERGENCY);
    expect($msg1->context)->toBe($context);

    expect($msg2->subject)->toBe($subject2);
    expect($msg2->level)->toBe(LogLevel::DEBUG);
    expect($msg2->context)->toBe($context);
});

it('associates parent models', function () {
    $user = new User();
    $user->id = 123;

    $this->message('Sending email...', $level = LogLevel::INFO)
        ->concerning($user)
        ->save();

    $this->gauge('duration', 120, 's', 'int')
        ->concerning($this->messages()->last())
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

it('nicely outputs messages to strings', function () {
    $this->messages()
        ->setContext('testing')
        ->warning('Message 1')
        ->alert('Message 2')
        ->save();

    /** @var Message $message */
    $messages = Message::query()->get();

    $dt1 = $messages->get('Message 1')->created_at->toDateTimeString('millisecond');

    $dt2 = $messages->get('Message 2')->created_at->toDateTimeString('millisecond');

    expect((string)$this->messages())->toBe(
        "$dt2 [alert] [testing] Message 2" . PHP_EOL
        . "$dt1 [warning] [testing] Message 1"
    );
});

it('nicely outputs gauges to strings', function () {
    $this->gauges()
        ->concerning($this->messages()->last())
        ->setContext('testing')
        ->gauge('duration', 120.3, 's')
        ->gauge('throughput', 10, ' messages/s')
        ->save();

    expect((string)$this->gauges())->toBe(
        '[testing] throughput: 10 messages/s' . PHP_EOL
        . '[testing] duration: 120.3s'
    );
});

it('serializes to array and json', function () {
    $this->message('Mail sent', LogLevel::NOTICE)
        ->setContext('testing')
        ->save();

    $this->gauge('duration', 120.3, 's')
        ->concerning($this->messages()->last())
        ->setContext('testing')
        ->save();

    /** @var Message $message */
    $message = $this->messages()->last();

    /** @var Gauge $gauge */
    $gauge = $message->gauges()->first();

    expect($arr = $message->toArray())->toBeArray();
    expect($json = $message->toJson())->toBeString();
    expect($arr['subject'])->toBe('Mail sent');
    expect($arr['context'])->toBe('testing');
    expect($json)->toContain(',"subject":"Mail sent",');

    expect($arr = $gauge->toArray())->toBeArray();
    expect($json = $gauge->toJson())->toBeString();

    expect($arr['context'])->toBe('testing');
    expect($json)->toContain(',"value":120.3,');
});

it('allows bulk creation of gauges', function () {
    $this->gauges([
        ['key' => 'test 1', 'value' => 1.2345678],
        new Gauge(['key' => 'test 2', 'value' => 2]),
        $this->gauge('test 3', 3),
    ])
        ->concerning($this->messages()->last())
        ->setContext('testing')
        ->save();

    $gauges = Gauge::orderBy('key')->get();

    expect($gauges->first()->key)->toBe('test 1');
    expect($gauges->first()->value)->toBe(1.2345678);
    expect($gauges->get('test 2')->value)->toBe(2);
    expect($gauges->last()->key)->toBe('test 3');
    expect($gauges->last()->value)->toBe(3);
});

it('allows to reset attributes', function () {
    $this->gauge('test 3', 3, 'cm')
        ->setKey('test 1')
        ->setValue(0)
        ->setUnit()
        ->setType(MologConstants::GAUGE_FLOAT_TYPE)
        ->save();

    /** @var Gauge $gauge */
    $gauge = Gauge::first();

    expect($gauge->key)->toBe('test 1');
    expect($gauge->value)->toBe(0.0);
    expect($gauge->unit)->toBeNull();
    expect($gauge->type)->toBe(MologConstants::GAUGE_FLOAT_TYPE);
});

it('doesn\'t allow invalid gauge data types', function () {
    $this->gauge('test', 1)->setType('array');
})->throws(MologException::class, "'array' is not a valid Gauge data type");

it('allows to clear the event relationship', function () {
    $this->message('HELLO')->save();

    $this->gauge('duration', 120.3, 's')
        ->concerning($this->messages()->last())
        ->concerning()
        ->save();

    $msg = Message::first();

    expect($msg->gauges()->get()->isEmpty())->toBeTrue();
});

it('calculates percentages', function () {
    $this->gauges()
        ->gauge('divided', 33)
        ->gauge('divider', 100)
        ->percentage('percentage', 'divided', 'divider');

    expect($this->gauges()->get('percentage')->value)->toBe(0.33);
});
