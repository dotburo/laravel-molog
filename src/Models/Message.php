<?php

namespace Dotburo\Molog\Models;

use Dotburo\Molog\Constants;
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
        'created_at' => 'datetime',
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
     * Return the code for the given level(s).
     * This returns the debug level code as fallback value.
     * @param string|string[] $levels
     * @return int|int[]
     */
    public static function levelCode($levels)
    {
        if (is_string($levels)) {
            $levels = explode(',', $levels);
        }

        $levels = array_intersect(
            array_map('trim', $levels),
            array_keys(Constants::LEVEL_CODES)
        );

        $codes = array_map(function($level) {
            return Constants::LEVEL_CODES[$level] ?? Constants::LEVEL_CODES[Constants::DEBUG];
        }, $levels);

        return count($levels) > 1 ? $codes : reset($codes);
    }

    /**
     * Return the code for the given level.
     * This returns the debug level code as fallback value.
     * @param int $level
     * @return string
     */
    public static function levelLabel(int $level): string
    {
        $levels = array_flip(Constants::LEVEL_CODES);

        return $levels[$level] ?? Constants::DEBUG;
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
