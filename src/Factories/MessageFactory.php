<?php

namespace Dotburo\Molog\Factories;

use Dotburo\Molog\Contracts\LoggerInterface;
use Dotburo\Molog\Models\Message;
use Dotburo\Molog\MologConstants;
use Dotburo\Molog\Traits\LoggerMethods;
use Throwable;

/**
 * Message factory class.
 *
 * @copyright 2021 dotburo
 * @author dotburo <code@dotburo.org>
 */
class MessageFactory extends EventFactory implements LoggerInterface
{
    use LoggerMethods;

    /**
     * Create a new message and add it to the collection.
     * @param string|Throwable $subject
     * @param string|int $level
     * @return self
     */
    public function message($subject, $level = MologConstants::MSG_DEFAULT_LEVEL): MessageFactory
    {
        return $this->log($subject, $level);
    }

    /**
     * Implements default "PSR" logging method.
     * {@inheritdoc}
     */
    public function log($subject, $level = MologConstants::MSG_DEFAULT_LEVEL): MessageFactory
    {
        $message = $subject instanceof Throwable
            ? Message::createFromException($subject, $level)
            : new Message([
                'level' => $level,
                'subject' => $subject,
            ]);

        $this->setGlobalProperties($message);

        $this->items->push($message);

        return $this;
    }
}
