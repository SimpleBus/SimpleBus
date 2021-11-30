<?php

namespace SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest;

use SimpleBus\Message\Name\NamedMessage;

final class SomeOtherEvent implements NamedMessage
{
    public static function messageName(): string
    {
        return 'some_other_event';
    }
}
