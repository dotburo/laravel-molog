<?php

namespace Dotburo\LogMetrics\Factories;

use Dotburo\LogMetrics\LogMetricsConstants;
use Dotburo\LogMetrics\Models\Metric;

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

    public function increment($key, $value = 0, string $type = LogMetricsConstants::DEFAULT_METRIC_TYPE, string $unit = ''): MetricFactory
    {
        /** @var Metric $metric */
        if ($metric = $this->items->where('key', $key)->first()) {
            $metric->setValueAttribute($metric->value + $value);

            $this->setLastUuid($metric->getKey());

            return $this;
        }

        return $this->add($key, $value, $type, $unit);
    }

    public function decrement($key, $value = 0, string $type = LogMetricsConstants::DEFAULT_METRIC_TYPE, string $unit = ''): MetricFactory
    {
        /** @var Metric $metric */
        if ($metric = $this->items->where('key', $key)->first()) {
            $metric->setValueAttribute($metric->value - $value);

            $this->setLastUuid($metric->getKey());

            return $this;
        }

        return $this->add($key, $value, $type, $unit);
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
        return $this->items->map(function(Metric $metric) {
            $context = $metric->context ? "$metric->context: " : '';

            return trim("→ {$context}$metric->key: $metric->value $metric->unit");
        })->reverse()->join(PHP_EOL);
    }
}
