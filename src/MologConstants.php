<?php

namespace Dotburo\Molog;

use Psr\Log\LogLevel;

/**
 * Defines client and api defaults.
 *
 * @copyright 2021 dotburo
 * @author dotburo <code@dotburo.org>
 */
final class MologConstants extends LogLevel
{
    /** @var int[] */
    public const LEVEL_CODES = [
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
    public const DEFAULT_GAUGE_TYPE = 'float';

    /** @var int */
    public const DEFAULT_GAUGE_ROUNDING = -1;

    /** @var string */
    public const CREATED_AT_FORMAT = 'Y-m-d H:i:s.u';
}
