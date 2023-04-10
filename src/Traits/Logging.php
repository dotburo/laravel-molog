<?php

namespace Dotburo\Molog\Traits;

use Dotburo\Molog\Factories\GaugeFactory;
use Dotburo\Molog\Factories\MessageFactory;
use Dotburo\Molog\Models\Event;
use Dotburo\Molog\Models\Gauge;
use Dotburo\Molog\Models\Message;
use Dotburo\Molog\MologConstants;
use Illuminate\Support\Collection;
use Throwable;

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
     * Return the factory for the parent object, instantiate if needed.
     * @return MessageFactory
     */
    protected function messageFactory(): MessageFactory
    {
        return $this->messageFactory ?? $this->messageFactory = new MessageFactory();
    }

    /**
     * Return the factory for the parent object, instantiate if needed.
     * @return GaugeFactory
     */
    protected function gaugeFactory(): GaugeFactory
    {
        return $this->gaugeFactory ?? $this->gaugeFactory = new GaugeFactory();
    }

    /**
     * Return the existing factory or instantiate a new one.
     * @param string|Throwable $subject
     * @param string $level
     * @return Message|Event
     */
    public function message($subject = '', string $level = MologConstants::MSG_DEFAULT_LEVEL): Message
    {
        $factory = $this->messageFactory();

        $this->messageFactory->log($subject, $level);

        return $factory->last();
    }

    /**
     * Return the existing factory or instantiate a new one.
     * @param string $key
     * @param int|float $value
     * @param string|null $unit
     * @param string $type
     * @return Gauge|Event
     */
    public function gauge(string $key = '', $value = 0, string $unit = '', string $type = MologConstants::GAUGE_DEFAULT_TYPE): Gauge
    {
        $factory = $this->gaugeFactory();

        $this->gaugeFactory->gauge($key, $value, $type, $unit);

        return $factory->last();
    }

    /**
     * Create a JobLog instance for the parent class.
     * @param Gauge[]|array[] $gauges
     * @return Collection
     */
    public function gauges(array $gauges = []): Collection
    {
        $factory = $this->gaugeFactory();

        if ($gauges) {
            $this->gaugeFactory->gauges($gauges);
        }

        return $factory->events();
    }
}
