<?php

namespace Dotburo\Molog\Factories;

use Dotburo\Molog\Constants;
use Dotburo\Molog\Models\Message;
use Psr\Log\LoggerInterface;

/**
 * Message factory class.
 *
 * @copyright 2021 dotburo
 * @author dotburo <code@dotburo.org>
 */
class MessageFactory extends EventFactory implements LoggerInterface
{
    /**
     * @param string $subject
     * @param string|int $level
     * @return $this
     */
    public function add(string $subject = '', $level = Constants::DEBUG): MessageFactory
    {
        $message = $subject instanceof Message
            ? $subject
            : new Message([
                'level' => $level,
                'subject' => $subject,
            ]);

        $message = $this->setGlobalProperties($message);

        $uuid = $message->getKey();

        $this->items->offsetSet($uuid, $message);

        $this->setLastUuid($uuid);

        return $this;
    }

    /**
     * Set the level last created/updated of the message.
     * @param string|int $level
     * @return $this
     */
    public function setLevel($level = Constants::DEBUG): MessageFactory
    {
        if ($message = $this->previous()) {
            $message->setLevelAttribute($level);
        }

        return $this;
    }

    /**
     * Set the level last created/updated of the message.
     * @param string|null $body
     * @return $this
     */
    public function setBody(?string $body = null): MessageFactory
    {
        if ($message = $this->previous()) {
            $message->setBodyAttribute($body);
        }

        return $this;
    }

    /**
     * Return the print-out of all current messages, most recent first.
     * @return string
     */
    public function __toString(): string
    {
        return $this->items->map(function (Message $message) {
            $context = $message->context ? "$message->context: " : '';

            return "$message->created_at [$message->level] {$context}$message->body";
        })->join(PHP_EOL);
    }

    public function emergency($message, array $context = []): MessageFactory
    {
        $this->add($message, Constants::EMERGENCY);

        return $this;
    }

    public function alert($message, array $context = []): MessageFactory
    {
        $this->add($message, Constants::ALERT);

        return $this;
    }

    public function critical($message, array $context = []): MessageFactory
    {
        $this->add($message, Constants::CRITICAL);

        return $this;
    }

    public function error($message, array $context = []): MessageFactory
    {
        $this->add($message, Constants::ERROR);

        return $this;
    }

    public function warning($message, array $context = []): MessageFactory
    {
        $this->add($message, Constants::WARNING);

        return $this;
    }

    public function notice($message, array $context = []): MessageFactory
    {
        $this->add($message, Constants::NOTICE);

        return $this;
    }

    public function info($message, array $context = []): MessageFactory
    {
        $this->add($message, Constants::INFO);

        return $this;
    }

    public function debug($message, array $context = []): MessageFactory
    {
        $this->add($message);

        return $this;
    }

    public function log($level, $message, array $context = []): MessageFactory
    {
        $this->add($message, $level);

        return $this;
    }
}
