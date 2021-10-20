<?php

namespace dotburo\LogMetrics\Factories;

use dotburo\LogMetrics\LogMetricsConstants;
use dotburo\LogMetrics\Models\Message;

class MessageFactory extends EventFactory
{
    public function __construct(string $body = '')
    {
        $this->model = new Message();

        if (!empty($body)) {
            $this->setBody($body);
        }
    }

    public function setLevel($level = LogMetricsConstants::DEBUG): MessageFactory
    {
        $this->model->setLevelAttribute($level);

        return $this;
    }

    public function setBody(string $body): MessageFactory
    {
        $this->model->setBodyAttribute($body);

        return $this;
    }
}
