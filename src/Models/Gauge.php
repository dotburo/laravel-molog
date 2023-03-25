<?php

namespace Dotburo\Molog\Models;

use Dotburo\Molog\Constants;

/**
 * Model for logged gauges.
 *
 * @property string $type
 * @property string $key
 * @property int|float $value
 * @property string $unit
 *
 * @copyright 2021 dotburo
 * @author dotburo <code@dotburo.org>
 */
class Gauge extends Event
{
    /** @inheritDoc  */
    protected $fillable = [
        'key', 'value', 'unit', 'type',
    ];

    /** @inheritDoc */
    protected $casts = [
        'value' => 'float',
        'created_at' => 'datetime',
    ];

    public function setTypeAttribute(?string $type): Gauge
    {
        $this->attributes['type'] = ! empty($type) ? strtolower($type) : Constants::DEFAULT_METRIC_TYPE;

        return $this;
    }

    public function setUnitAttribute(?string $unit): Gauge
    {
        $this->attributes['unit'] = $unit ?: null;

        return $this;
    }

    public function setKeyAttribute(string $key): Gauge
    {
        $this->attributes['key'] = $key;

        return $this;
    }

    public function setValueAttribute($value): Gauge
    {
        $this->attributes['value'] = $value ?: 0;

        return $this;
    }

    public function getValueAttribute()
    {
        $value = $this->attributes['value'];

        settype($value, $this->attributes['type']);

        return $value;
    }
}
