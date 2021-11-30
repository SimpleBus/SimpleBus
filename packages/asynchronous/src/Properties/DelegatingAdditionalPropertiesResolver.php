<?php

namespace SimpleBus\Asynchronous\Properties;

final class DelegatingAdditionalPropertiesResolver implements AdditionalPropertiesResolver
{
    /**
     * @var AdditionalPropertiesResolver[]
     */
    private array $resolvers;

    /**
     * @param AdditionalPropertiesResolver[] $resolvers
     */
    public function __construct(array $resolvers)
    {
        $this->resolvers = $resolvers;
    }

    /**
     * Combine properties.
     *
     * @return mixed[]
     */
    public function resolveAdditionalPropertiesFor(object $message): array
    {
        $properties = [];

        foreach ($this->resolvers as $resolver) {
            $properties = array_merge($properties, $resolver->resolveAdditionalPropertiesFor($message));
        }

        return $properties;
    }
}
