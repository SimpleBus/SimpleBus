<?php

namespace SimpleBus\Message\Tests\CallableResolver\Fixtures;

final class LegacySubscriber
{
    public function notify(object $message): void
    {
    }
}
