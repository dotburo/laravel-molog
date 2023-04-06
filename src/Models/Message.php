<?php

namespace Dotburo\Molog\Models;

use Dotburo\Molog\Exceptions\MologException;
use Dotburo\Molog\MologConstants;
use Dotburo\Molog\Traits\PsrLoggerMethods;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Psr\Log\LoggerInterface;

/**
 * Model for logged messages.
 *
 * @property int $level
 * @property string $subject
 * @property string $body
 *
 * @copyright 2021 dotburo
 * @author dotburo <code@dotburo.org>
 */
class Message extends Event implements LoggerInterface
{
    use PsrLoggerMethods;

    /** @inheritDoc  */
    protected $fillable = [
        'level', 'subject', 'body',
    ];

    /** @inheritDoc */
    protected $casts = [
        'level' => 'int',
        'created_at' => 'datetime',
    ];

    /** @inheritdoc */
    public static function boot()
    {
        parent::boot();

        static::saving(function (Message $message) {
            if (!$message->subject) {
                throw new MologException('A message without subject cannot be savec');
            }
        });
    }

    /**
     * Optional relationship with child gauges.
     * @return MorphMany
     */
    public function gauges(): MorphMany
    {
        return $this->morphMany(Gauge::class, 'loggable');
    }

    /**
     * Implements default PSR logging method.
     * {@inheritdoc}
     */
    public function log($level, $subject, array $context = []): Message
    {
        $this->level = $level;

        $this->subject = $subject;

        foreach (['context', 'body', 'user_id', 'tenant_id'] as $property) {
            if (isset($context[$property])) {
                $this->$property = $context[$property];
            }
        }

        return $this;
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
            array_keys(MologConstants::LEVEL_CODES)
        );

        $codes = array_map(function($level) {
            return MologConstants::LEVEL_CODES[$level] ?? MologConstants::LEVEL_CODES[MologConstants::DEBUG];
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
        $levels = array_flip(MologConstants::LEVEL_CODES);

        return $levels[$level] ?? MologConstants::DEBUG;
    }

    /**
     * Public setter.
     * @param $level
     * @return $this
     */
    public function setLevel($level): Message
    {
        return $this->setLevelAttribute($level);
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
    protected function setLevelAttribute($level): Message
    {
        $this->attributes['level'] = is_numeric($level) ? (int)$level : static::levelCode($level);

        return $this;
    }

    /**
     * Public setter.
     * @param string $subject
     * @return Message
     */
    public function setSubject(string $subject): Message
    {
        return $this->setSubjectAttribute($subject);
    }

    /**
     * Make sure the body is set as a string or null.
     * @param string $subject
     * @return Message
     */
    protected function setSubjectAttribute(string $subject): Message
    {
        $this->attributes['subject'] = $subject;

        return $this;
    }

    /**
     * Public setter.
     * @param string $body
     * @return Message
     */
    public function setBody(string $body = ''): Message
    {
        return $this->setBodyAttribute($body);
    }

    /**
     * Make sure the body is a non-empty string or null.
     * @param string $body
     * @return Message
     */
    protected function setBodyAttribute(string $body = ''): Message
    {
        $this->attributes['body'] = $body ?: null;

        return $this;
    }

    /**
     * Override Laravel's method to construct a standard log line.
     * @return string
     */
    public function __toString(): string
    {
        $context = $this->context ? " [$this->context] " : ' ';

        return "$this->created_at [$this->level]{$context}$this->subject";
    }
}
