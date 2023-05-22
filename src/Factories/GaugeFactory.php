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
        $gauge = $key instanceof Gauge ? $key : $this->items->get($key);

        if (! $key instanceof Gauge && $gauge) {
            $gauge->setValue($value)->setUnit($unit);
        } else {
            $gauge = new Gauge([
                'key' => $key,
                'value' => $value,
                'unit' => $unit,
            ]);
        }

        $gauge = $this->setGlobalProperties($gauge);

        $this->items->offsetSet($gauge->key, $gauge);

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
        if ($gauge = $this->items[$key] ?? null) {
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
        if ($gauge = $this->items[$key] ?? null) {
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
        $gauge = $this->items[$key] ?? null;

        if (! $gauge) {
            throw new MologException("The timer '$key' has not been started yet");
        }

        $gauge->setValue(microtime(true) - $gauge->value);

        return $this;
    }

    public function percentage(string $key, $divided, $divider): GaugeFactory
    {
        $divider = isset($this->items[$divider]) ? $this->items[$divider]->value : 0;

        $divided = isset($this->items[$divided]) ? $this->items[$divided]->value : 0;

        $percentage = $divider ? ($divided / $divider) : 0;

        return $this->gauge($key, $percentage * 100, '%');
    }
}
