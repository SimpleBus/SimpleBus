<?php

namespace SimpleBus\JMSSerializerBundleBridge\Tests\Functional;

use JMS\Serializer\Annotation as Serializer;

final class SampleMessage
{
    /**
     * @Serializer\Type("string")
     */
    private string $foo;

    /**
     * @Serializer\Type("integer")
     */
    private int $bar;

    public function __construct(string $foo, int $bar)
    {
        $this->foo = $foo;
        $this->bar = $bar;
    }

    public function getFoo(): string
    {
        return $this->foo;
    }

    public function getBar(): int
    {
        return $this->bar;
    }
}
