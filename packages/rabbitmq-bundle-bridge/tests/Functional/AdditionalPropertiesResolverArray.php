<?php

namespace SimpleBus\RabbitMQBundleBridge\Tests\Functional;

use SimpleBus\Asynchronous\Properties\AdditionalPropertiesResolver;

final class AdditionalPropertiesResolverArray implements AdditionalPropertiesResolver
{
    /**
     * @var array<string, string>
     */
    private array $data;

    /**
     * @param array<string, string> $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return array<string, string>
     */
    public function resolveAdditionalPropertiesFor(object $message): array
    {
        return $this->data;
    }
}
