<?php

namespace SimpleBus\RabbitMQBundleBridge\Tests\Functional;

use OldSound\RabbitMqBundle\RabbitMq\Producer;

class AdditionalPropertiesResolverProducerMock extends Producer
{
    /**
     * @var array
     */
    private $additionalProperties;

    public function getAdditionalProperties()
    {
        return $this->additionalProperties;
    }

    public function publish($msgBody, $routingKey = '', $additionalProperties = array())
    {
        $this->additionalProperties = $additionalProperties;
    }
}
