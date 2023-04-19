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
    /** @var string */
    public const CREATED_AT_FORMAT = 'Y-m-d\TH:i:s.u\Z';

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
    public const MSG_DEFAULT_LEVEL = parent::DEBUG;

    /** @var string */
    public const GAUGE_INT_TYPE = 'int';

    /** @var string */
    public const GAUGE_FLOAT_TYPE = 'float';

    /** @var string */
    public const GAUGE_DEFAULT_TYPE = self::GAUGE_FLOAT_TYPE;

    /** @var int */
    public const GAUGE_DEFAULT_ROUNDING = -1;
}
