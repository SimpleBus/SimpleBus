<?php

namespace SimpleBus\SymfonyBridge\Tests\Functional;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Tools\SchemaTool;
use SimpleBus\Message\Bus\MessageBus;
use SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\DoctrineTestKernel;
use SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\SomeOtherEventSubscriber;
use SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\SomeOtherTestCommandHandler;
use SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\TestCommand;
use SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\TestCommandHandler;
use SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\TestEntityCreatedEventSubscriber;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DoctrineOrmSmokeTest extends KernelTestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();

        static::$class = null;
    }

    /**
     * @test
     */
    public function itHandlesACommandThenDispatchesEventsForAllModifiedEntities(): void
    {
        self::bootKernel(['environment' => 'config1']);
        $container = self::$kernel->getContainer();

        $this->createSchema($container);

        $command = new TestCommand();
        $commandBus = $container->get('command_bus');

        $this->assertInstanceOf(MessageBus::class, $commandBus);

        $commandBus->handle($command);

        $testCommandHandler = $container->get('test_command_handler');
        $this->assertInstanceOf(TestCommandHandler::class, $testCommandHandler);
        $this->assertTrue($testCommandHandler->commandHandled);

        $testEventSubscriber = $container->get('test_event_subscriber');
        $this->assertInstanceOf(TestEntityCreatedEventSubscriber::class, $testEventSubscriber);
        $this->assertTrue($testEventSubscriber->eventHandled);

        // some_other_test_command is triggered by test_event_handler
        $someOtherTestCommandHandler = $container->get('some_other_test_command_handler');
        $this->assertInstanceOf(SomeOtherTestCommandHandler::class, $someOtherTestCommandHandler);
        $this->assertTrue($someOtherTestCommandHandler->commandHandled);

        $someOtherEventSubscriber = $container->get('some_other_event_subscriber');
        $this->assertInstanceOf(SomeOtherEventSubscriber::class, $someOtherEventSubscriber);
        $this->assertTrue($someOtherEventSubscriber->eventHandled);

        // it has logged some things
        $logFile = $container->getParameter('log_file');
        $loggedMessages = is_string($logFile) ? (file_get_contents($logFile) ?: '') : '';
        $this->assertStringContainsString('command_bus.DEBUG: Started handling a message', $loggedMessages);
        $this->assertStringContainsString('command_bus.DEBUG: Finished handling a message', $loggedMessages);
        $this->assertStringContainsString('event_bus.DEBUG: Started handling a message', $loggedMessages);
        $this->assertStringContainsString('event_bus.DEBUG: Finished handling a message', $loggedMessages);
        $this->assertStringContainsString('event_bus.DEBUG: Started notifying a subscriber', $loggedMessages);
        $this->assertStringContainsString('event_bus.DEBUG: Finished notifying a subscriber', $loggedMessages);
    }

    protected static function getKernelClass(): string
    {
        return DoctrineTestKernel::class;
    }

    private function createSchema(ContainerInterface $container): void
    {
        /** @var EntityManager $entityManager */
        $entityManager = $container->get('doctrine.orm.entity_manager');

        /** @var array<int, ClassMetadata<object>> $metadata */
        $metadata = $entityManager->getMetadataFactory()->getAllMetadata();

        $schemaTool = new SchemaTool($entityManager);
        $schemaTool->createSchema($metadata);
    }
}
