<?php

namespace dotburo\LogMetrics\Models;

/**
 * Model for logged metrics.
 *
 * @property string $type
 * @property string $key
 * @property int|float $value
 * @property string $unit
 *
 * @copyright 2021 dotburo
 * @author dotburo <code@dotburo.org>
 */
class Metric extends Event
{
    /** @inheritDoc */
    protected $casts = [
        'value' => 'float',
        'created_at' => 'datetime'
    ];

    public function setTypeAttribute(string $type): void
    {
    }

    public function setUnitAttribute(string $unit): void
    {

    }

    public function setKeyAttribute(string $key): void
    {

    }

    public function setValueAttribute($value): void
    {

    }
}
