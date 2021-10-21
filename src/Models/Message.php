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

    /**
     * Return the code for the given level.
     * This returns the debug level code as default and fallback value.
     * @param int $level
     * @return string
     */
    public static function levelLabel(int $level): string
    {
        $levels = array_flip(LogMetricsConstants::LEVEL_CODES);

        return $levels[$level] ?? $levels[LogMetricsConstants::DEBUG];
    }

    /**
     * Always display the level with its label.
     * @return string
     */
    public function getLevelAttribute(): string
    {
        return self::levelLabel($this->attributes['level']);
    }

    /**
     * Make sure the level is always stored as a valid int.
     * @param $level
     * @return void
     */
    public function setLevelAttribute($level): void
    {
        $this->attributes['level'] = is_numeric($level) ? $level : static::levelCode($level);
    }

    /**
     * Make sure the body is set as a string or null.
     * @param string $body
     * @return void
     */
    public function setBodyAttribute(string $body): void
    {
        $this->attributes['body'] = $body ?: null;
    }
}
