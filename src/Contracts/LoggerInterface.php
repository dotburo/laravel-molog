<?php

namespace Dotburo\Molog\Contracts;

use Dotburo\Molog\MologConstants;
use Throwable;

/**
 * Describes a logger instance.
 *
 * This loosely follows the PSR LoggerInterface, but it allows the implementing class to return itself.
 * Contrary to the original interface, this does not make use of a `context` argument in its methods.
 * All additional attributes can be set separately by through the implementing classes, eg. {@see EventInterface}.
 *
 * @copyright 2023 dotburo
 * @author dotburo <code@dotburo.org>
 */
interface LoggerInterface
{
    /**
     * System is unusable.
     * @param string $subject
     * @return self
     */
    public function emergency(string $subject): self;

    /**
     * Action must be taken immediately.
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     * @param string $subject
     * @return self
     */
    public function alert(string $subject): self;

    /**
     * Critical conditions.
     * Example: Application component unavailable, unexpected exception.
     * @param string $subject
     * @return self
     */
    public function critical(string $subject): self;

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     * @param string $subject
     * @return self
     */
    public function error(string $subject): self;

    /**
     * Exceptional occurrences that are not errors.
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     * @param string $subject
     * @return self
     */
    public function warning(string $subject): self;

    /**
     * Normal but significant events.
     * @param string $subject
     * @return self
     */
    public function notice(string $subject): self;

    /**
     * Interesting events.
     * Example: User logs in, SQL logs.
     * @param string $subject
     * @return self
     */
    public function info(string $subject): self;

    /**
     * Detailed debug information.
     * @param string $subject
     * @return self
     */
    public function debug(string $subject): self;

    /**
     * Logs with an arbitrary level.
     * @param string|Throwable $subject
     * @param string|int $level
     * @return self
     */
    public function log($subject, $level = MologConstants::MSG_DEFAULT_LEVEL): self;
}
