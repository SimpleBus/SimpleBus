<?php

namespace SimpleBus\BernardBundleBridge\Tests;

use Bernard\Message\DefaultMessage;
use SimpleBus\BernardBundleBridge\BernardConsumer;

class BernardConsumerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_should_consume_a_message()
    {
        $consumer = $this->getMock('SimpleBus\Asynchronous\Consumer\SerializedEnvelopeConsumer');
        $consumer
            ->expects($this->once())
            ->method('consume')
            ->with($this->equalTo('__data__'))
        ;

        $bernard = new BernardConsumer($consumer);

        $bernard(new DefaultMessage('name', ['data' => '__data__']));
    }
}
