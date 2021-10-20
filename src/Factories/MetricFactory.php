<?php

namespace dotburo\LogMetrics\Factories;

use dotburo\LogMetrics\Models\Metric;

class MetricFactory extends EventFactory
{
    public function __construct(string $key, $value)
    {
        $this->model = new Metric();

        $this->setValue($key, $value);
    }

    public function setType(string $type = 'float'): MetricFactory
    {
        $this->model->setTypeAttribute($type);

        return $this;
    }

    public function setUnit(string $unit): MetricFactory
    {
        $this->model->setUnitAttribute($unit);

        return $this;
    }

    public function setValue(string $key, $value): MetricFactory
    {
        $this->model->setKeyAttribute($key);

        $this->model->setValueAttribute($value);

        return $this;
    }
}
