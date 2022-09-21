<?php

namespace Dotburo\LogMetrics\Factories;

use Dotburo\LogMetrics\LogMetricsConstants;
use Dotburo\LogMetrics\Models\Metric;
use Illuminate\Support\Collection;

/**
 * Metric factory class.
 *
 * @copyright 2021 dotburo
 * @author dotburo <code@dotburo.org>
 */
class MetricFactory extends EventFactory
{
    public function add($key, $value = 0, string $type = LogMetricsConstants::DEFAULT_METRIC_TYPE, string $unit = ''): MetricFactory
    {
        $metric = $key instanceof Metric
            ? $key
            : new Metric([
                'key' => $key,
                'value' => $value,
                'unit' => $unit,
                'type' => $type,
            ]);

        $metric = $this->setGlobalProperties($metric);

        $uuid = $metric->getKey();

        $this->items->offsetSet($uuid, $metric);

        $this->setLastUuid($uuid);

        return $this;
    }

    public function addMany(array $items): MetricFactory
    {
        foreach ($items as $metric) {
            $this->add($metric['key'], $metric['value'], $metric['type'] ?? '', $metric['unit'] ?? '');
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
    public function increment(string $key, $value = 1, string $type = LogMetricsConstants::DEFAULT_METRIC_TYPE, string $unit = ''): MetricFactory
    {
        /** @var Metric $metric */
        if ($metric = $this->getMetricsByKey($key)->first()) {
            $metric->setValueAttribute($metric->value + $value);

            $this->setLastUuid($metric->getKey());

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
    public function decrement(string $key, $value = 1, string $type = LogMetricsConstants::DEFAULT_METRIC_TYPE, string $unit = ''): MetricFactory
    {
        /** @var Metric $metric */
        if ($metric = $this->getMetricsByKey($key)->first()) {
            $metric->setValueAttribute($metric->value - $value);

            $this->setLastUuid($metric->getKey());

            return $this;
        }

        return $this->add($key, $value, $type, $unit);
    }

    /**
     * @param string $key
     * @param bool $contextRelative
     * @return Collection
     */
    protected function getMetricsByKey(string $key, bool $contextRelative = true): Collection
    {
        return $this->items->filter(function (Metric $metric) use ($key, $contextRelative) {
            return $metric->key === $key && (! $contextRelative || $metric->context === $this->context);
        });
    }

    /**
     * Initiate a timing measurement.
     * @param string $key
     * @return $this
     */
    public function startTimer(string $key = 'duration'): MetricFactory
    {
        return $this->add($key, microtime(true), 'float', 's');
    }

    /**
     * Calculate the time difference with a previously set metric with the same key.
     * @param string $key
     * @return $this
     */
    public function stopTimer(string $key = 'duration'): MetricFactory
    {
        if (! ($metric = $this->items->where('key', $key)->first())) {
            return $this;
        }

        $metric->setValueAttribute(microtime(true) - $metric->value);

        $this->setLastUuid($metric->getKey());

        return $this;
    }

    public function setType(string $type = LogMetricsConstants::DEFAULT_METRIC_TYPE): MetricFactory
    {
        if ($event = $this->previous()) {
            $event->setTypeAttribute($type);
        }

        return $this;
    }

    public function setUnit(string $unit): MetricFactory
    {
        if ($event = $this->previous()) {
            $event->setUnitAttribute($unit);
        }

        return $this;
    }

    public function setKey(string $key): MetricFactory
    {
        if ($event = $this->previous()) {
            $event->setKeyAttribute($key);
        }

        return $this;
    }

    public function setValue($value): MetricFactory
    {
        if ($event = $this->previous()) {
            $event->setValueAttribute($value);
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->items->map(function (Metric $metric) {
            $context = $metric->context ? "$metric->context: " : '';

            return trim("→ {$context}$metric->key: $metric->value $metric->unit");
        })->reverse()->join(PHP_EOL);
    }
}
