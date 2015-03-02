<?php

require __DIR__ . '/../bootstrap.php';

use SimpleBus\RabbitMQBundle\Tests\Functional\TestKernel;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Debug\Debug;

$input = new ArgvInput();

Debug::enable();

$kernel = new TestKernel('test', true);
$application = new Application($kernel);
$application->run($input);
