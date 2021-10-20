<?php

namespace dotburo\LogMetrics\Models;

use dotburo\LogMetrics\LogMetricsConstants;

/**
 * Model for logged messages.
 *
 * @property int $level
 * @property string $body
 *
 * @copyright 2021 dotburo
 * @author dotburo <code@dotburo.org>
 */
class Message extends Event
{
    /** @inheritDoc */
    protected $casts = [
        'level' => 'int',
        'created_at' => 'datetime'
    ];

    /**
     * Return the code for the given level.
     * This returns the debug level code as default and fallback value.
     * @param string $level
     * @return int
     */
    public static function levelCode(string $level): int
    {
        return LogMetricsConstants::LEVEL_CODES[$level] ?? LogMetricsConstants::LEVEL_CODES['debug'];
    }

    public function setLevelAttribute($level): void
    {
        $this->attributes['level'] = is_numeric($level) ? $level : static::levelCode($level);
    }

    public function setBodyAttribute(string $body): void
    {
        $this->attributes['body'] = $body ?: null;
    }
}
