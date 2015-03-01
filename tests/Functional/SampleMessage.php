<?php

namespace SimpleBus\JMSSerializerBundle\Tests\Functional;

use SimpleBus\Message\Message;
use JMS\Serializer\Annotation as Serializer;

class SampleMessage implements Message
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
