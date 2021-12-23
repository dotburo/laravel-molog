<?php

namespace Dotburo\LogMetrics;

use Psr\Log\LogLevel;

/**
 * Defines client and api defaults.
 *
 * @copyright 2021 dotburo
 * @author dotburo <code@dotburo.org>
 */
final class LogMetricsConstants extends LogLevel
{
    /** @var int[] */
    const LEVEL_CODES = [
        parent::EMERGENCY => 0,
        parent::ALERT => 1,
        parent::CRITICAL => 2,
        parent::ERROR => 3,
        parent::WARNING => 4,
        parent::NOTICE => 5,
        parent::INFO => 6,
        parent::DEBUG => 7,
    ];

    /** @var string */
    const DEFAULT_METRIC_TYPE = 'float';
}
