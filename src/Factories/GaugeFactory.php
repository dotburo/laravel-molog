<?php

namespace Dotburo\Molog\Factories;

use Dotburo\Molog\Constants;
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
    public function add($key, $value = 0, string $type = Constants::DEFAULT_METRIC_TYPE, string $unit = ''): GaugeFactory
    {
        $gauge = $key instanceof Gauge
            ? $key
            : new Gauge([
                'key' => $key,
                'value' => $value,
                'unit' => $unit,
                'type' => $type,
            ]);

        $gauge = $this->setGlobalProperties($gauge);

        $uuid = $gauge->getKey();

        $this->items->offsetSet($uuid, $gauge);

        $this->setLastUuid($uuid);

        return $this;
    }

    public function addMany(array $items): GaugeFactory
    {
        foreach ($items as $gauge) {
            $this->add($gauge['key'], $gauge['value'], $gauge['type'] ?? '', $gauge['unit'] ?? '');
        }

        return $this;
    }

    /**
     * Increment the metric for the given key and value.
     * @param string $key
     * @param int|float $value
     * @param string $type
     * @param string $unit
     * @return $this
     */
    public function increment(string $key, $value = 1, string $type = Constants::DEFAULT_METRIC_TYPE, string $unit = ''): GaugeFactory
    {
        /** @var Gauge $gauge */
        if ($gauge = $this->getGaugesByKey($key)->first()) {
            $gauge->setValueAttribute($gauge->value + $value);

            $this->setLastUuid($gauge->getKey());

            return $this;
        }

        return $this->add($key, $value, $type, $unit);
    }

    /**
     * Decrement the metric for the given key and value.
     * @param string $key
     * @param int|float $value
     * @param string $type
     * @param string $unit
     * @return $this
     */
    public function decrement(string $key, $value = 1, string $type = Constants::DEFAULT_METRIC_TYPE, string $unit = ''): GaugeFactory
    {
        /** @var Gauge $gauge */
        if ($gauge = $this->getGaugesByKey($key)->first()) {
            $gauge->setValueAttribute($gauge->value - $value);

            $this->setLastUuid($gauge->getKey());

            return $this;
        }

        return $this->add($key, $value, $type, $unit);
    }

    /**
     * @param string $key
     * @param bool $contextRelative
     * @return Collection
     */
    protected function getGaugesByKey(string $key, bool $contextRelative = true): Collection
    {
        return $this->items->filter(function (Gauge $gauge) use ($key, $contextRelative) {
            return $gauge->key === $key && (! $contextRelative || $gauge->context === $this->context);
        });
    }

    /**
     * Initiate a timing measurement.
     * @param string $key
     * @return $this
     */
    public function startTimer(string $key = 'duration'): GaugeFactory
    {
        return $this->add($key, microtime(true), 'float', 's');
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

        $this->setLastUuid($gauge->getKey());

        return $this;
    }

    public function setType(string $type = Constants::DEFAULT_METRIC_TYPE): GaugeFactory
    {
        if ($event = $this->previous()) {
            $event->setTypeAttribute($type);
        }

        return $this;
    }

    public function setUnit(string $unit): GaugeFactory
    {
        if ($event = $this->previous()) {
            $event->setUnitAttribute($unit);
        }

        return $this;
    }

    public function setKey(string $key): GaugeFactory
    {
        if ($event = $this->previous()) {
            $event->setKeyAttribute($key);
        }

        return $this;
    }

    public function setValue($value): GaugeFactory
    {
        if ($event = $this->previous()) {
            $event->setValueAttribute($value);
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->items->map(function (Gauge $gauge) {
            $context = $gauge->context ? "$gauge->context: " : '';

            return trim("→ {$context}$gauge->key: $gauge->value $gauge->unit");
        })->reverse()->join(PHP_EOL);
    }
}
