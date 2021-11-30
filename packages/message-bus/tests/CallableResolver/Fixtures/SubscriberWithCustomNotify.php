<?php

namespace SimpleBus\Message\Tests\CallableResolver\Fixtures;

final class SubscriberWithCustomNotify
{
    public function customNotifyMethod(object $message): void
    {
    }
}
