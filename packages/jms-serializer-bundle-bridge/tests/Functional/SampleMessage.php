<?php

namespace SimpleBus\JMSSerializerBundleBridge\Tests\Functional;

use JMS\Serializer\Annotation as Serializer;

class SampleMessage
{
    /**
     * @Serializer\Type("string")
     */
    private $foo;

    /**
     * @Serializer\Type("integer")
     */
    private $bar;

    public function __construct($foo, $bar)
    {
        $this->foo = $foo;
        $this->bar = $bar;
    }
}
