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
    public function emergency(string $subject): self
    {
        return $this->log($subject, MologConstants::EMERGENCY);
    }

    /** @inheritdoc */
    public function alert(string $subject): self
    {
        return $this->log($subject, MologConstants::ALERT);
    }

    /** @inheritdoc */
    public function critical(string $subject): self
    {
        return $this->log($subject, MologConstants::CRITICAL);
    }

    /** @inheritdoc */
    public function error(string $subject): self
    {
        return $this->log($subject, MologConstants::ERROR);
    }

    /** @inheritdoc */
    public function warning(string $subject): self
    {
        return $this->log($subject, MologConstants::WARNING);
    }

    /** @inheritdoc */
    public function notice(string $subject): self
    {
        return $this->log($subject, MologConstants::NOTICE);
    }

    /** @inheritdoc */
    public function info(string $subject): self
    {
        return $this->log($subject, MologConstants::INFO);
    }

    /** @inheritdoc */
    public function debug(string $subject): self
    {
        return $this->log($subject);
    }
}
