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
     * @param string|Throwable $subject
     * @return self
     */
    public function emergency($subject): self;

    /**
     * Action must be taken immediately.
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     * @param string|Throwable $subject
     * @return self
     */
    public function alert($subject): self;

    /**
     * Critical conditions.
     * Example: Application component unavailable, unexpected exception.
     * @param string|Throwable $subject
     * @return self
     */
    public function critical($subject): self;

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     * @param string|Throwable $subject
     * @return self
     */
    public function error($subject): self;

    /**
     * Exceptional occurrences that are not errors.
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     * @param string|Throwable $subject
     * @return self
     */
    public function warning($subject): self;

    /**
     * Normal but significant events.
     * @param string|Throwable $subject
     * @return self
     */
    public function notice($subject): self;

    /**
     * Interesting events.
     * Example: User logs in, SQL logs.
     * @param string|Throwable $subject
     * @return self
     */
    public function info($subject): self;

    /**
     * Detailed debug information.
     * @param string|Throwable $subject
     * @return self
     */
    public function debug($subject): self;

    /**
     * Logs with an arbitrary level.
     * @param string|Throwable $subject
     * @param string|int $level
     * @return self
     */
    public function log($subject, $level = MologConstants::MSG_DEFAULT_LEVEL): self;
}
