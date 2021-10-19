<?php

namespace Dotburo\LogMetrics;

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
    protected $guarded = [
        'id', 'tenant_id', 'context', 'context_id', 'type', 'key', 'value', 'unit', 'created_at',
    ];

    /** @inheritDoc */
    protected $casts = [
        'value' => 'float',
        'created_at' => 'datetime'
    ];
}
