<?php

namespace Dotburo\LogMetrics;

use Dotburo\LogMetrics\Factories\MessageFactory;
use Dotburo\LogMetrics\Factories\MetricFactory;
use Dotburo\LogMetrics\Models\Metric;

/**
 * Provides logging.
 *
 * @copyright 2021 dotburo
 * @author dotburo <code@dotburo.org>
 */
trait Logging
{
    /** @var MessageFactory */
    private MessageFactory $messageFactory;

    /** @var MetricFactory */
    private MetricFactory $metricFactory;

    /**
     * Return the existing factory or instantiate a new one.
     * @param string $body
     * @param string $level
     * @return MessageFactory
     */
    public function message(string $body = '', string $level = LogMetricsConstants::DEBUG): MessageFactory
    {
        $factory = $this->messageFactory ?? $this->messageFactory = new MessageFactory();

        if ($body) {
            $this->messageFactory->add($body, $level);
        }

        return $factory;
    }

    /**
     * Return the existing factory or instantiate a new one.
     * @param string $key
     * @param int|float $value
     * @param string|null $unit
     * @param string $type
     * @return MetricFactory
     */
    public function metric(string $key = '', $value = null, string $unit = '', string $type = LogMetricsConstants::DEFAULT_METRIC_TYPE): MetricFactory
    {
        $factory = $this->metricFactory ?? $this->metricFactory = new MetricFactory();

        if ($key) {
            $this->metricFactory->add($key, $value, $unit, $type);
        }

        return $factory;
    }

    /**
     * Create a JobLog instance for the parent class.
     * @param Metric[]|array[] $metrics
     * @return MetricFactory
     */
    public function metrics(array $metrics = []): MetricFactory
    {
        $factory = $this->metricFactory ?? $this->metricFactory = new MetricFactory();

        if ($metrics) {
            $this->metricFactory->addMany($metrics);
        }

        return $factory;
    }
}
