<?php

namespace SimpleBus\RabbitMQBundle\Event;

final class Events
{
    /**
     * This event will be dispatched whenever an AMQP message has been successfully consumed by the message bus
     */
    const MESSAGE_CONSUMED = 'simple_bus.rabbit_mq.message_consumed';

    /**
     * This event will be dispatched whenever an AMQP message could not be processed by the message bus
     */
    const MESSAGE_CONSUMPTION_FAILED = 'simple_bus.rabbit_mq.message_consumption_failed';
}
