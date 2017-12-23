<?php

namespace SimpleBus\BernardBundleBridge\Tests;

use Bernard\Message\PlainMessage;
use SimpleBus\BernardBundleBridge\BernardConsumer;

/**
 * @group BernardBundleBridge
 */
class BernardConsumerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function it_should_consume_a_message()
    {
        $consumer = $this->createMock('SimpleBus\Asynchronous\Consumer\SerializedEnvelopeConsumer');
        $consumer
            ->expects($this->once())
            ->method('consume')
            ->with($this->equalTo('__data__'))
        ;

        $bernard = new BernardConsumer($consumer);

        $bernard(new PlainMessage('name', ['data' => '__data__']));
    }
}
