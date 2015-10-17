<?php

namespace SimpleBus\BernardBundleBridge\Tests\Functional\Demo;

/**
 * Copied from https://github.com/SimpleBus/RabbitMQBundleBridge/blob/master/tests/Functional/AlwaysFailingCommandHandler.php.
 */
class AlwaysFailingCommandHandler
{
    public function handle()
    {
        throw new \Exception('I always fail');
    }
}
