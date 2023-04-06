<?php

namespace Dotburo\Molog\Traits;

use Dotburo\Molog\MologConstants;
use Dotburo\Molog\Models\Message;

/**
 * PSR compliant methods.
 *
 * @copyright 2023 dotburo
 * @author dotburo <code@dotburo.org>
 */
trait PsrLoggerMethods
{
    /** @inheritdoc */
    public function emergency($subject, array $context = []): self
    {
        return $this->log(MologConstants::EMERGENCY, $subject, $context);
    }

    /** @inheritdoc */
    public function alert($subject, array $context = []): self
    {
        return $this->log(MologConstants::ALERT, $subject, $context);
    }

    /** @inheritdoc */
    public function critical($subject, array $context = []): self
    {
        return $this->log(MologConstants::CRITICAL, $subject, $context);
    }

    /** @inheritdoc */
    public function error($subject, array $context = []): self
    {
        return $this->log(MologConstants::ERROR, $subject, $context);
    }

    /** @inheritdoc */
    public function warning($subject, array $context = []): self
    {
        return $this->log(MologConstants::WARNING, $subject, $context);
    }

    /** @inheritdoc */
    public function notice($subject, array $context = []): self
    {
        return $this->log(MologConstants::NOTICE, $subject, $context);
    }

    /** @inheritdoc */
    public function info($subject, array $context = []): self
    {
        return $this->log(MologConstants::INFO, $subject, $context);
    }

    /** @inheritdoc */
    public function debug($subject, array $context = []): self
    {
        return $this->log(MologConstants::DEBUG, $subject, $context);
    }
}
