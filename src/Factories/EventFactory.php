<?php

namespace dotburo\LogMetrics\Factories;

use dotburo\LogMetrics\Models\Message;
use dotburo\LogMetrics\Models\Metric;

class EventFactory
{
    /** @var Message|Metric */
    protected $model;

    /**
     * Instantiate a message factory.
     * @param string|null $body
     * @return MessageFactory
     */
    public static function createMessage(string $body = ''): MessageFactory
    {
        return new MessageFactory($body);
    }

    public static function createMetric(string $key, $value): MetricFactory
    {
        return new MetricFactory($key, $value);
    }

    /**
     * Set the properties on the model and return the factory.
     * @param string $label
     * @return $this
     */
    public function setContext(string $label = ''): EventFactory
    {
        $this->model->setContextAttribute($label);

        return $this;
    }

    /**
     * Set the property on the model and return the factory.
     * @param int $id
     * @return $this
     */
    public function setTenant(int $id = 0): EventFactory
    {
        $this->model->setTenantIdAttribute($id);

        return $this;
    }

    /**
     * @return bool
     */
    public function log(): bool
    {
        return $this->model->save();
    }
}
