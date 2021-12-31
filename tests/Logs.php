<?php

namespace Dotburo\LogMetrics\Tests;

use Dotburo\LogMetrics\Logging;
use Dotburo\LogMetrics\LogMetricsConstants;
use Exception;

class Logs
{
    use Logging;

    public function handle()
    {
        $message = $this->message()
            ->notice('Sent')
            ->setContext('App\Mail\Mailable')
            ->setRelation(4, 'App\Models\User')
            ->last();

        $message->setLevelAttribute(LogMetricsConstants::ERROR)
            ->setBodyAttribute(new Exception('Sending error'));

        $this->message()->save();

        return $this->message();
    }
}
