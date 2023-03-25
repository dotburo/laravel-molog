<?php

namespace Dotburo\Molog;

use Dotburo\Molog\Factories\MessageFactory;
use Dotburo\Molog\Factories\GaugeFactory;
use Dotburo\Molog\Models\Gauge;

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

    /** @var GaugeFactory */
    private GaugeFactory $gaugeFactory;

    /**
     * Return the existing factory or instantiate a new one.
     * @param string $subject
     * @param string $level
     * @return MessageFactory
     */
    public function message(string $subject = '', string $level = Constants::DEBUG): MessageFactory
    {
        $factory = $this->messageFactory ?? $this->messageFactory = new MessageFactory();

        if ($subject) {
            $this->messageFactory->add($subject, $level);
        }

        return $factory;
    }

    /**
     * Return the existing factory or instantiate a new one.
     * @param string $key
     * @param int|float $value
     * @param string|null $unit
     * @param string $type
     * @return GaugeFactory
     */
    public function gauge(string $key = '', $value = 0, string $type = Constants::DEFAULT_METRIC_TYPE, string $unit = ''): GaugeFactory
    {
        $factory = $this->gaugeFactory ?? $this->gaugeFactory = new GaugeFactory();

        if ($key) {
            $this->gaugeFactory->add($key, $value, $type, $unit);
        }

        return $factory;
    }

    /**
     * Create a JobLog instance for the parent class.
     * @param Gauge[]|array[] $gauges
     * @return GaugeFactory
     */
    public function gauges(array $gauges = []): GaugeFactory
    {
        $factory = $this->gaugeFactory ?? $this->gaugeFactory = new GaugeFactory();

        if ($gauges) {
            $this->gaugeFactory->addMany($gauges);
        }

        return $factory;
    }
}
