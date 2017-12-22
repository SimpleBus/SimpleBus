<?php

namespace SimpleBus\RabbitMQBundleBridge\Tests\Functional;

use SimpleBus\Asynchronous\Properties\AdditionalPropertiesResolver;

class AdditionalPropertiesResolverArray implements AdditionalPropertiesResolver
{
    /**
     * @var array
     */
    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function resolveAdditionalPropertiesFor($message)
    {
        return $this->data;
    }
}
