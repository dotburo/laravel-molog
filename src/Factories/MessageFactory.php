<?php

namespace Dotburo\LogMetrics\Factories;

use Dotburo\LogMetrics\LogMetricsConstants;
use Dotburo\LogMetrics\Models\Message;
use Dotburo\LogMetrics\Models\Metric;
use Psr\Log\LoggerInterface;

/**
 * Message factory class.
 *
 * @method emergency($message, array $context = array())
 * @method alert($message, array $context = array())
 * @method critical($message, array $context = array())
 * @method error($message, array $context = array())
 * @method warning($message, array $context = array())
 * @method notice($message, array $context = array())
 * @method info($message, array $context = array())
 * @method debug($message, array $context = array())
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

    public function log($level, $message, array $context = []): MessageFactory
    {
        $this->add($message, $level);

        return $this;
    }

    public function __call($name, $arguments)
    {
        if (isset(LogMetricsConstants::LEVEL_CODES[$name])) {
            return $this->log($name, ...$arguments);
        }

        return parent::__call($name, $arguments);
    }

    public function __toString(): string
    {
        return $this->items->map(function(Message $message) {
            return "$message->created_at [$message->level] $message->body";
        })->join(PHP_EOL);
    }
}
