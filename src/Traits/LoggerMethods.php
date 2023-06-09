<?php

namespace Dotburo\Molog\Traits;

use Dotburo\Molog\MologConstants;

/**
 * Implements the PSR compliant methods.
 *
 * @copyright 2023 dotburo
 * @author dotburo <code@dotburo.org>
 */
trait LoggerMethods
{
    /** @inheritdoc */
    public function emergency($subject): self
    {
        return $this->log($subject, MologConstants::EMERGENCY);
    }

    /** @inheritdoc */
    public function alert($subject): self
    {
        return $this->log($subject, MologConstants::ALERT);
    }

    /** @inheritdoc */
    public function critical($subject): self
    {
        return $this->log($subject, MologConstants::CRITICAL);
    }

    /** @inheritdoc */
    public function error($subject): self
    {
        return $this->log($subject, MologConstants::ERROR);
    }

    /** @inheritdoc */
    public function warning($subject): self
    {
        return $this->log($subject, MologConstants::WARNING);
    }

    /** @inheritdoc */
    public function notice($subject): self
    {
        return $this->log($subject, MologConstants::NOTICE);
    }

    /** @inheritdoc */
    public function info($subject): self
    {
        return $this->log($subject, MologConstants::INFO);
    }

    /** @inheritdoc */
    public function debug($subject): self
    {
        return $this->log($subject);
    }
}
