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
    public function add($key, $value = null, string $type = LogMetricsConstants::DEFAULT_METRIC_TYPE, string $unit = ''): MetricFactory
    {
        $metric = $key instanceof Metric
            ? $key
            : new Metric([
                'key' => $key,
                'value' => $value,
                'unit' => $unit,
                'type' => $type
            ]);

        $this->items->offsetSet($metric->getKey(), $metric);

        return $this;
    }

    public function addMany(array $items): MetricFactory
    {
        foreach ($items as $metric) {
            $this->add($metric['key'], $metric['value'], $metric['type'] ?? '', $metric['unit'] ?? '');
        }

        return $this;
    }

    public function setType(string $id, string $type = LogMetricsConstants::DEFAULT_METRIC_TYPE): MetricFactory
    {
        /** @var Metric|null $metric */
        if ($metric = $this->items->get($id)) {
            $metric->setTypeAttribute($type);
        }

        return $this;
    }

    public function setUnit(string $id, string $unit): MetricFactory
    {
        /** @var Metric|null $metric */
        if ($metric = $this->items->get($id)) {
            $metric->setUnitAttribute($unit);
        }

        return $this;
    }

    public function setKey(string $id, string $key): MetricFactory
    {
        /** @var Metric|null $metric */
        if ($metric = $this->items->get($id)) {
            $metric->setKeyAttribute($key);
        }

        return $this;
    }

    public function setValue(string $id, $value): MetricFactory
    {
        /** @var Metric|null $metric */
        if ($metric = $this->items->get($id)) {
            $metric->setValueAttribute($value);
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->items->map(function(Metric $metric) {
            return trim("→ $metric->key: $metric->value $metric->unit");
        })->join(PHP_EOL);
    }
}
