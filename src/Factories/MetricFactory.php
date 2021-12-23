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
    public function add($key, $value = null, string $unit = '', string $type = LogMetricsConstants::DEFAULT_METRIC_TYPE): MetricFactory
    {
        $metric = $key instanceof Metric
            ? $key
            : new Metric([
                'key' => $key,
                'value' => $value,
                'unit' => $unit,
                'type' => $type
            ]);

        $this->items->add($metric);

        return $this;
    }

    public function addMany(array $metrics): MetricFactory
    {
        foreach ($metrics as $model) {
            $model = $model instanceof Metric ? $model : new Metric($model);

            $this->add($model);
        }

        return $this;
    }

    public function setType(string $key, string $type = LogMetricsConstants::DEFAULT_METRIC_TYPE): MetricFactory
    {
        /** @var Metric|null $metric */
        if ($metric = $this->items->get($key)) {
            $this->items->add($metric->setTypeAttribute($type));
        }

        return $this;
    }

    public function setUnit(string $key, string $unit): MetricFactory
    {
        /** @var Metric|null $metric */
        if ($metric = $this->items->get($key)) {
            $this->items->add($metric->setUnitAttribute($unit));
        }

        return $this;
    }

    public function setKey(string $old, string $new): MetricFactory
    {
        /** @var Metric|null $metric */
        if ($metric = $this->items->pull($old)) {
            $metric = $metric->setKeyAttribute($new);

            $this->items->add($metric);
        }

        return $this;
    }

    public function setValue(string $key, $value): MetricFactory
    {
        /** @var Metric|null $metric */
        if ($metric = $this->items->get($key)) {
            $this->items->offsetSet($key, $metric->setValueAttribute($value));
        }

        return $this;
    }
}
