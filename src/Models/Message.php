<?php

namespace Dotburo\LogMetrics\Models;

use Dotburo\LogMetrics\LogMetricsConstants;
use Illuminate\Database\Eloquent\Relations\MorphMany;

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
    /** @inheritDoc  */
    protected $fillable = [
        'level', 'body',
    ];

    /** @inheritDoc */
    protected $casts = [
        'level' => 'int',
        'created_at' => 'datetime'
    ];

    /**
     * Optional relationship with child metrics.
     * @return MorphMany
     */
    public function metrics(): MorphMany
    {
        return $this->morphMany(Metric::class, 'loggable');
    }

    /**
     * Return the code for the given level.
     * This returns the debug level code as fallback value.
     * @param string $level
     * @return int
     */
    public static function levelCode(string $level): int
    {
        return LogMetricsConstants::LEVEL_CODES[$level] ?? LogMetricsConstants::LEVEL_CODES[LogMetricsConstants::DEBUG];
    }

    /**
     * Return the code for the given level.
     * This returns the debug level code as fallback value.
     * @param int $level
     * @return string
     */
    public static function levelLabel(int $level): string
    {
        $levels = array_flip(LogMetricsConstants::LEVEL_CODES);

        return $levels[$level] ?? LogMetricsConstants::DEBUG;
    }

    /**
     * Always display the level with its label.
     * @return string
     */
    public function getLevelAttribute(): string
    {
        return static::levelLabel($this->attributes['level']);
    }

    /**
     * Make sure the level is always stored as a valid int.
     * @param $level
     * @return Message
     */
    public function setLevelAttribute($level): Message
    {
        $this->attributes['level'] = is_numeric($level) ? (int)$level : static::levelCode($level);

        return $this;
    }

    /**
     * Make sure the body is set as a string or null.
     * @param string $body
     * @return Message
     */
    public function setBodyAttribute(string $body): Message
    {
        $this->attributes['body'] = $body ?: null;

        return $this;
    }
}
