<?php

namespace SimpleBus\RabbitMQBundleBridge\Tests\Functional;

use OldSound\RabbitMqBundle\RabbitMq\Producer;

final class AdditionalPropertiesResolverProducerMock extends Producer
{
    /**
     * @var mixed[]
     */
    private array $additionalProperties;

    /**
     * @return mixed[]
     */
    public function getAdditionalProperties(): array
    {
        return $this->additionalProperties;
    }

    /**
     * @param string       $msgBody
     * @param string       $routingKey
     * @param mixed[]      $additionalProperties
     * @param null|mixed[] $headers
     */
    public function publish($msgBody, $routingKey = '', $additionalProperties = [], array $headers = null): void
    {
        $this->additionalProperties = $additionalProperties;
    }
}
