<?php

namespace Dotburo\Molog\Models;

use Dotburo\Molog\MologConstants;

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
    /** @inheritdoc  */
    protected $fillable = [
        'key', 'value', 'unit', 'type',
    ];

    /** @inheritdoc */
    protected $casts = [
        'value' => 'float',
        'created_at' => 'datetime',
    ];

    public function setTypeAttribute(?string $type): Gauge
    {
        $this->attributes['type'] = ! empty($type) ? strtolower($type) : MologConstants::DEFAULT_GAUGE_TYPE;

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

        $round = (int)config('molog.gauge_float_round', MologConstants::DEFAULT_GAUGE_ROUNDING);

        if ($round > -1) {
            return round($value, $round);
        }

        return $value;
    }

    /**
     * Override Laravel's method to construct a standard log line.
     * @return string
     */
    public function __toString(): string
    {
        $context = $this->context ? "[$this->context] " : '';

        return "{$context}$this->key: $this->value{$this->unit}";
    }
}
