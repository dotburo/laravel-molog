<?php

namespace Dotburo\LogMetrics;

/**
 * Provides logging.
 *
 * @copyright 2021 dotburo
 * @author dotburo <code@dotburo.org>
 */
trait Logging
{
    /**
     * Log instance for the parent class.
     * @var Message
     */
    public Message $logged;

    /**
     * Create a JobLog instance for the parent class.
     * @return void
     */
    public function createLog(): void
    {
        $this->logged = Message::createLog($name);
    }


}
