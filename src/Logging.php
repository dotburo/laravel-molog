<?php

namespace dotburo\LogMetrics;

use dotburo\LogMetrics\Factories\EventFactory;
use dotburo\LogMetrics\Factories\MessageFactory;
use dotburo\LogMetrics\Factories\MetricFactory;

/**
 * Provides logging.
 *
 * @copyright 2021 dotburo
 * @author dotburo <code@dotburo.org>
 */
trait Logging
{
    /**
     * Create a JobLog instance for the parent class.
     * @param string $body
     * @return MessageFactory
     */
    public function message(string $body = ''): MessageFactory
    {
        return EventFactory::createMessage($body);
    }

    /**
     * Create a JobLog instance for the parent class.
     * @param string $key
     * @param int|float $value
     * @return MetricFactory
     */
    public function metric(string $key, $value): MetricFactory
    {
        return EventFactory::createMetric($key, $value);
    }
}
