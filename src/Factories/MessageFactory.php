<?php

namespace Dotburo\LogMetrics\Factories;

use Dotburo\LogMetrics\LogMetricsConstants;
use Dotburo\LogMetrics\Models\Message;
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
     * @param string $body
     * @param string|int $level
     * @return $this
     */
    public function add(string $body = '', $level = LogMetricsConstants::DEBUG): MessageFactory
    {
        $message = $body instanceof Message
            ? $body
            : new Message([
                'level' => $level,
                'body' => $body,
            ]);

        $this->items->offsetSet($message->getKey(), $message);

        return $this;
    }

    /**
     * @param string $id
     * @param string|int $level
     * @return $this
     */
    public function setLevel(string $id, $level = LogMetricsConstants::DEBUG): MessageFactory
    {
        /** @var Message|null $message */
        if ($message = $this->items->get($id)) {
            $message->setLevelAttribute($level);
        }

        return $this;
    }

    public function setBody(string $id, string $body): MessageFactory
    {
        /** @var Message|null $message */
        if ($message = $this->items->get($id)) {
            $message->setBodyAttribute($body);
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->items->map(function(Message $message) {
            return "$message->created_at [$message->level] $message->body";
        })->join(PHP_EOL);
    }

    public function emergency($message, array $context = []): MessageFactory
    {
        $this->add($message, LogMetricsConstants::EMERGENCY);

        return $this;
    }

    public function alert($message, array $context = []): MessageFactory
    {
        $this->add($message, LogMetricsConstants::ALERT);

        return $this;
    }

    public function critical($message, array $context = []): MessageFactory
    {
        $this->add($message, LogMetricsConstants::CRITICAL);

        return $this;
    }

    public function error($message, array $context = []): MessageFactory
    {
        $this->add($message, LogMetricsConstants::ERROR);

        return $this;
    }

    public function warning($message, array $context = []): MessageFactory
    {
        $this->add($message, LogMetricsConstants::WARNING);

        return $this;
    }

    public function notice($message, array $context = []): MessageFactory
    {
        $this->add($message, LogMetricsConstants::NOTICE);

        return $this;
    }

    public function info($message, array $context = []): MessageFactory
    {
        $this->add($message, LogMetricsConstants::INFO);

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
