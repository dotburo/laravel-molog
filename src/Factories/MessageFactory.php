<?php

namespace Dotburo\Molog\Factories;

use Dotburo\Molog\Models\Message;
use Dotburo\Molog\Traits\PsrLoggerMethods;
use Psr\Log\LoggerInterface;

/**
 * Message factory class.
 *
 * @copyright 2021 dotburo
 * @author dotburo <code@dotburo.org>
 */
class MessageFactory extends EventFactory implements LoggerInterface
{
    use PsrLoggerMethods;

    /**
     * Implements default PSR logging method.
     * {@inheritdoc}
     */
    public function log($level, $subject, array $context = []): MessageFactory
    {
        $message = $subject instanceof Message
            ? $subject
            : new Message([
                'level' => $level,
                'subject' => $subject,
            ]);

        $this->setGlobalProperties($message);

        $this->items->push($message);

        return $this;
    }

    /**
     * Provide similar interface as `GaugeFactory::gauge()`.
     * @param mixed $level
     * @param string $subject
     * @param array $context
     * @return $this
     */
    public function message($level, $subject, array $context = []): MessageFactory
    {
        return $this->log($level, $subject, $context);
    }
}
