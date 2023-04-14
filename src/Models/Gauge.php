<?php

namespace Dotburo\Molog\Models;

use Dotburo\Molog\Exceptions\MologException;
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
    /** @inheritdoc */
    protected $table = 'gauges';

    /** @inheritdoc */
    protected $attributes = [
        'type' => MologConstants::GAUGE_DEFAULT_TYPE,
    ];

    /** @inheritdoc */
    protected $casts = [
        'value' => 'float',
        'user_id' => 'int',
        'tenant_id' => 'int',
    ];

    /**
     * Public setter.
     * @param string $key
     * @return $this
     */
    public function setKey(string $key): Gauge
    {
        return $this->setKeyAttribute($key);
    }

    /**
     * Laravel mutator.
     * @param string $key
     * @return $this
     */
    protected function setKeyAttribute(string $key): Gauge
    {
        $this->attributes['key'] = trim($key);

        return $this;
    }

    /**
     * Public setter.
     * @param float|int $value
     * @return $this
     */
    public function setValue($value): Gauge
    {
        return $this->setValueAttribute($value);
    }

    /**
     * Make sure the value is casted as soon as it is set.
     * @param float|int $value
     * @return $this
     */
    protected function setValueAttribute($value): Gauge
    {
        if (! $value) {
            $this->attributes['value'] = 0;

            return $this;
        }

        $this->attributes['type'] = is_float($value) ? MologConstants::GAUGE_FLOAT_TYPE : MologConstants::GAUGE_INT_TYPE;

        $this->attributes['value'] = (float)$value;

        return $this;
    }

    /**
     * Return the value of the metric as int or (rounded) float.
     * @return float|int
     */
    protected function getValueAttribute()
    {
        $value = $this->attributes['value'];

        # The value is always stored as float, we need to cast it to int if the type attribute requires it.
        if ($this->attributes['type'] === MologConstants::GAUGE_INT_TYPE) {
            return (int)$value;
        }

        $round = app('config')->get('molog.gauge_float_round') ?? MologConstants::GAUGE_DEFAULT_ROUNDING;

        return $round > -1 ? round($value, (int)$round) : $value;
    }

    /**
     * Public setter.
     * @param string $type
     * @return $this
     * @throws MologException
     */
    public function setType(string $type): Gauge
    {
        return $this->setTypeAttribute($type);
    }

    /**
     * Laravel mutator.
     * @param string $type
     * @return $this
     * @throws MologException
     */
    protected function setTypeAttribute(string $type): Gauge
    {
        if ($type !== MologConstants::GAUGE_FLOAT_TYPE && $type !== MologConstants::GAUGE_INT_TYPE) {
            throw new MologException("'$type' is not a valid Gauge data type");
        }

        $this->attributes['type'] = $type;

        return $this;
    }

    /**
     * Public setter.
     * @param string $unit
     * @return $this
     */
    public function setUnit(string $unit = ''): Gauge
    {
        return $this->setUnitAttribute($unit);
    }

    /**
     * Laravel mutator, make sure to cast to null if empty.
     * @param string $unit
     * @return $this
     */
    protected function setUnitAttribute(string $unit = ''): Gauge
    {
        $this->attributes['unit'] = $unit ?: null;

        return $this;
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
