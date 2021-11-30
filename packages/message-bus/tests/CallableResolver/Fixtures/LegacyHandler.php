<?php

namespace SimpleBus\Message\Tests\CallableResolver\Fixtures;

final class LegacyHandler
{
    public function handle(object $message): void
    {
    }
}
