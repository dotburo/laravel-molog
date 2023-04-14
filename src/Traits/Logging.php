<?php

namespace Dotburo\Molog\Traits;

use Dotburo\Molog\Factories\GaugeFactory;
use Dotburo\Molog\Factories\MessageFactory;
use Dotburo\Molog\Models\Event;
use Dotburo\Molog\Models\Gauge;
use Dotburo\Molog\Models\Message;
use Dotburo\Molog\MologConstants;
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
    protected function messages(): MessageFactory
    {
        return $this->messageFactory ?? $this->messageFactory = new MessageFactory();
    }

    /**
     * Return the factory for the parent object, instantiate if needed.
     * @param array $gauges
     * @return GaugeFactory
     */
    protected function gauges(array $gauges = []): GaugeFactory
    {
        $this->gaugeFactory ?? $this->gaugeFactory = new GaugeFactory();

        if ($gauges) {
            $this->gaugeFactory->gauges($gauges);
        }

        return $this->gaugeFactory;
    }

    /**
     * Return the existing factory or instantiate a new one.
     * @param string|Throwable $subject
     * @param string $level
     * @return Message|Event
     */
    public function message($subject = '', string $level = MologConstants::MSG_DEFAULT_LEVEL): Message
    {
        $factory = $this->messages();

        $this->messageFactory->log($subject, $level);

        return $factory->last();
    }

    /**
     * Return the existing factory or instantiate a new one.
     * @param string $key
     * @param int|float $value
     * @param string|null $unit
     * @return Gauge|Event
     */
    public function gauge(string $key = '', $value = 0, string $unit = ''): Gauge
    {
        $factory = $this->gauges();

        $this->gaugeFactory->gauge($key, $value, $unit);

        return $factory->last();
    }
}
