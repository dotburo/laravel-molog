<?php

namespace Dotburo\Molog\Factories;

use Dotburo\Molog\Models\Gauge;
use Illuminate\Support\Collection;

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
        $gauge = $key instanceof Gauge ? $key : $this->collection()->firstWhere('key', $key);

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
        if ($gauge = $this->getGaugesByKey($key)->first()) {
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
        if ($gauge = $this->getGaugesByKey($key)->first()) {
            /** @var Gauge $gauge */
            $gauge->setValue($gauge->value - $value);

            return $this;
        }

        return $this->gauge($key, $value, $unit);
    }

    /**
     * @param string $key
     * @return Collection
     */
    protected function getGaugesByKey(string $key): Collection
    {
        return $this->items->filter(function (Gauge $gauge) use ($key) {
            return $gauge->key === $key;
        });
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
     */
    public function stopTimer(string $key = 'duration'): GaugeFactory
    {
        if (! ($gauge = $this->items->where('key', $key)->first())) {
            return $this;
        }

        $gauge->setValueAttribute(microtime(true) - $gauge->value);

        return $this;
    }
}
