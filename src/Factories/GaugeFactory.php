<?php

namespace Dotburo\Molog\Factories;

use Dotburo\Molog\Exceptions\MologException;
use Dotburo\Molog\Models\Gauge;

/**
 * Gauge factory class.
 *
 * @copyright 2021 dotburo
 * @author dotburo <code@dotburo.org>
 */
class GaugeFactory extends EventFactory
{
    /**
     * Create a new metric and add it to the collection.
     * @param Gauge|string $key
     * @param int|float $value
     * @param string $unit
     * @return $this
     */
    public function gauge($key, $value = 0, string $unit = ''): GaugeFactory
    {
        $gauge = $key instanceof Gauge ? $key : $this->items->firstWhere('key', $key);

        if (!$key instanceof Gauge && $gauge) {
            $gauge->setValue($value)->setUnit($unit);
        } else {
            $gauge = new Gauge([
                'key' => $key,
                'value' => $value,
                'unit' => $unit,
            ]);
        }

        $gauge = $this->setGlobalProperties($gauge);

        $this->items->push($gauge);

        return $this;
    }

    /**
     * Create multiple gauges at once.
     * @param array $items
     * @return $this
     */
    public function gauges(array $items): GaugeFactory
    {
        foreach ($items as $gauge) {
            $this->gauge($gauge['key'], $gauge['value'], $gauge['unit'] ?? '');
        }

        return $this;
    }

    /**
     * Increment the metric for the given key and value.
     * @param string $key
     * @param int|float $value
     * @param string $unit
     * @return $this
     */
    public function increment(string $key, $value = 1, string $unit = ''): GaugeFactory
    {
        if ($gauge = $this->items->firstWhere('key', $key)) {
            /** @var Gauge $gauge */
            $gauge->setValue($gauge->value + $value);

            return $this;
        }

        return $this->gauge($key, $value, $unit);
    }

    /**
     * Decrement the metric for the given key and value.
     * @param string $key
     * @param int|float $value
     * @param string $unit
     * @return $this
     */
    public function decrement(string $key, $value = 1, string $unit = ''): GaugeFactory
    {
        if ($gauge = $this->items->firstWhere('key', $key)) {
            /** @var Gauge $gauge */
            $gauge->setValue($gauge->value - $value);

            return $this;
        }

        return $this->gauge($key, -1, $unit);
    }

    /**
     * Initiate a timing measurement.
     * @param string $key
     * @return $this
     */
    public function startTimer(string $key = 'duration'): GaugeFactory
    {
        return $this->gauge($key, microtime(true), 's');
    }

    /**
     * Calculate the time difference with a previously set gauge with the same key.
     * @param string $key
     * @return $this
     * @throws MologException
     */
    public function stopTimer(string $key = 'duration'): GaugeFactory
    {
        if (! ($gauge = $this->items->firstWhere('key', $key))) {
            throw new MologException("The timer '$key' has not been started yet");
        }

        $gauge->setValue(microtime(true) - $gauge->value);

        return $this;
    }
}
