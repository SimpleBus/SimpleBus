<?php

namespace SimpleBus\RabbitMQBundleBridge\Tests;

use OldSound\RabbitMqBundle\RabbitMq\Producer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SimpleBus\Asynchronous\Properties\AdditionalPropertiesResolver;
use SimpleBus\Asynchronous\Routing\RoutingKeyResolver;
use SimpleBus\RabbitMQBundleBridge\RabbitMQPublisher;
use SimpleBus\Serialization\Envelope\Serializer\MessageInEnvelopeSerializer;
use stdClass;

final class RabbitMQPublisherTest extends TestCase
{
    /**
     * @test
     */
    public function itSerializesTheMessageAndPublishesItUsingTheResolvedRouterKey(): void
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

    /**
     * @return MessageInEnvelopeSerializer|MockObject
     */
    private function mockSerializer()
    {
        return $this->createMock(MessageInEnvelopeSerializer::class);
    }

    /**
     * @return MockObject|Producer
     */
    private function mockProducer()
    {
        return $this
            ->getMockBuilder(Producer::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function dummyMessage(): stdClass
    {
        return new stdClass();
    }

    /**
     * @return MockObject|RoutingKeyResolver
     */
    private function routingKeyResolverStub(object $message, string $routingKey)
    {
        $resolver = $this->createMock(RoutingKeyResolver::class);
        $resolver
            ->expects($this->any())
            ->method('resolveRoutingKeyFor')
            ->with($this->identicalTo($message))
            ->will($this->returnValue($routingKey));

        return $resolver;
    }

    /**
     * @param mixed[] $additionalProperties
     *
     * @return AdditionalPropertiesResolver|MockObject
     */
    private function additionalPropertiesResolverStub(object $message, array $additionalProperties)
    {
        $resolver = $this->createMock(AdditionalPropertiesResolver::class);
        $resolver
            ->expects($this->any())
            ->method('resolveAdditionalPropertiesFor')
            ->with($this->identicalTo($message))
            ->will($this->returnValue($additionalProperties));

        return $resolver;
    }
}
