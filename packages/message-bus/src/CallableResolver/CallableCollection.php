<?php

namespace SimpleBus\Message\CallableResolver;

use Assert\Assertion;

class CallableCollection
{
    /**
     * @var array<string, callable[]>
     */
    private array $callablesByName;

    private CallableResolver $callableResolver;

    /**
     * @param array<string, callable[]> $callablesByName
     */
    public function __construct(
        array $callablesByName,
        CallableResolver $callableResolver
    ) {
        Assertion::allIsArray($callablesByName, 'You need to provide arrays of callables, indexed by name');

        $this->callablesByName = $callablesByName;
        $this->callableResolver = $callableResolver;
    }

    /**
     * @return callable[]
     */
    public function filter(string $name): array
    {
        if (!array_key_exists($name, $this->callablesByName)) {
            return [];
        }

        $callables = $this->callablesByName[$name];

        return array_map([$this->callableResolver, 'resolve'], $callables);
    }
}
