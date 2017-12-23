<?php

namespace SimpleBus\BernardBundleBridge\Tests;

use Bernard\Message;
use Bernard\Producer;
use SimpleBus\BernardBundleBridge\BernardPublisher;

/**
 * @group BernardBundleBridge
 */
class BernardPublisherTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function it_should_produce_a_message_with_bernard()
    {
        $message = new \stdClass();

        $queueResolver = $this->createMock('SimpleBus\Asynchronous\Routing\RoutingKeyResolver');
        $queueResolver
            ->expects($this->once())
            ->method('resolveRoutingKeyFor')
            ->with($this->equalTo($message))
            ->willReturn('queue_name')
        ;

        $serializer = $this->createMock('SimpleBus\Serialization\Envelope\Serializer\MessageInEnvelopeSerializer');
        $serializer
            ->expects($this->once())
            ->method('wrapAndSerialize')
            ->with($this->equalTo($message))
            ->willReturn('serialized_data')
        ;

        $producer = new CaptureProducer();

        $publisher = new BernardPublisher($serializer, $producer, $queueResolver, 'foo');
        $publisher->publish($message);

        $this->assertInstanceOf('Bernard\Message\PlainMessage', $producer->message);
        $this->assertEquals('queue_name', $producer->queueName);
        $this->assertEquals('serialized_data', $producer->message->get('data'));
        $this->assertEquals('foo', $producer->message->get('type'));
        $this->assertEquals('queue_name', $producer->message->getName());
    }
}

class CaptureProducer extends Producer
{
    /** @var \Bernard\Message\PlainMessage */
    public $message;
    public $queueName;

    public function __construct()
    {
    }

    public function produce(Message $message, $queueName = null)
    {
        $this->message = $message;
        $this->queueName = $queueName;
    }
}
