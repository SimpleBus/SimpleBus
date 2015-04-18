<?php

namespace SimpleBus\RabbitMQBundleBridge\Tests\Functional;

use SimpleBus\Asynchronous\Properties\AdditionalPropertiesResolver;
use SimpleBus\Message\Message;

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
