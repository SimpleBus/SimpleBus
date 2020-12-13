<?php

namespace SimpleBus\RabbitMQBundleBridge\Tests;

use PHPUnit\Framework\TestCase;
use SimpleBus\RabbitMQBundleBridge\RabbitMQPublisher;

/**
 * @internal
 * @coversNothing
 */
class RabbitMQPublisherTest extends TestCase
{
    /**
     * @test
     */
    public function itSerializesTheMessageAndPublishesItUsingTheResolvedRouterKey()
    {
        $message = $this->dummyMessage();
        $routingKey = 'the-routing-key';
        $serializedMessageEnvelope = 'the-serialized-message-envelope';
        $serializer = $this->mockSerializer();
        $serializer
            ->expects($this->once())
            ->method('wrapAndSerialize')
            ->with($message)
            ->will($this->returnValue($serializedMessageEnvelope));

        $producer = $this->mockProducer();
        $producer
            ->expects($this->once())
            ->method('publish')
            ->with($this->identicalTo($serializedMessageEnvelope), $this->identicalTo($routingKey));

        $routingKeyResolver = $this->routingKeyResolverStub($message, $routingKey);

        $additionalPropertiesResolver = $this->additionalPropertiesResolverStub($message, []);

        $publisher = new RabbitMQPublisher($serializer, $producer, $routingKeyResolver, $additionalPropertiesResolver);

        $publisher->publish($message);
    }

    private function mockSerializer()
    {
        return $this->createMock('SimpleBus\Serialization\Envelope\Serializer\MessageInEnvelopeSerializer');
    }

    private function mockProducer()
    {
        return $this
            ->getMockBuilder('OldSound\RabbitMqBundle\RabbitMq\Producer')
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function dummyMessage()
    {
        return new \stdClass();
    }

    private function routingKeyResolverStub($message, $routingKey)
    {
        $resolver = $this->createMock('SimpleBus\Asynchronous\Routing\RoutingKeyResolver');
        $resolver
            ->expects($this->any())
            ->method('resolveRoutingKeyFor')
            ->with($this->identicalTo($message))
            ->will($this->returnValue($routingKey));

        return $resolver;
    }

    private function additionalPropertiesResolverStub($message, $additionalProperties)
    {
        $resolver = $this->createMock('SimpleBus\Asynchronous\Properties\AdditionalPropertiesResolver');
        $resolver
            ->expects($this->any())
            ->method('resolveAdditionalPropertiesFor')
            ->with($this->identicalTo($message))
            ->will($this->returnValue($additionalProperties));

        return $resolver;
    }
}
