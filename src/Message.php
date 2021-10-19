<?php

namespace Dotburo\LogMetrics;

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
    protected $guarded = [
        'id', 'tenant_id', 'context', 'context_id', 'level', 'body', 'created_at',
    ];

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
        return Constants::LEVEL_CODES[$level] ?? Constants::LEVEL_CODES['debug'];
    }

    /**
     * Instantiate a logger.
     * @param string $caller
     * @return static
     */
    public function create(string $caller)
    {
        $log = new static();

        $log->logger = $caller;

        $instance = new static([
            'level' => $level,
            'context' => $context,
            'message' => $message,
            'uri' => strpos($uri, 'http') === 0 ? parse_url($uri, PHP_URL_PATH) : $uri,
            'trace' => is_array($trace) || is_object($trace) ? json_encode($trace) : $trace,
        ]);

        $instance->save();

        return $instance;
    }

    public function __call($method, $parameters)
    {
        if (in_array($method, ['debug', 'info'])) {
            return (new static())->create('info', ...$parameters);
        }

        return parent::__call($method, $parameters); // TODO: Change the autogenerated stub
    }
}
