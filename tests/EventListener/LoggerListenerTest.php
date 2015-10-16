<?php

namespace SimpleBus\BernardBundleBridge\Tests\EventListener;

use Bernard\Envelope;
use Bernard\Event\EnvelopeEvent;
use Bernard\Event\RejectEnvelopeEvent;
use Bernard\Message\DefaultMessage;
use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;
use SimpleBus\BernardBundleBridge\EventListener\LoggerListener;
use Symfony\Component\EventDispatcher\EventDispatcher;

class LoggerListenerTest extends \PHPUnit_Framework_TestCase
{
    /** @var DebugLogger */
    private $logger;

    /** @var EventDispatcher */
    private $dispatcher;

    public function setUp()
    {
        $this->logger = new DebugLogger();
        $this->dispatcher = new EventDispatcher();
        $this->dispatcher->addSubscriber(new LoggerListener($this->logger));
    }

    /**
     * @test
     */
    public function it_should_log_on_produce()
    {
        $message  = new DefaultMessage('test', ['type' => 'command']);
        $envelope = new Envelope($message);
        $event    = new EnvelopeEvent($envelope, $this->getMock('Bernard\Queue'));

        $this->dispatcher->dispatch('bernard.produce', $event);

        $this->assertArrayHasKey(LogLevel::INFO, $this->logger->records);
        $this->assertCount(1, $this->logger->records[LogLevel::INFO]);

        $record = $this->logger->records[LogLevel::INFO][0];

        $this->assertEquals('Produced command into "test" queue', $record[0]);
        $this->assertSame($message, $record[1]['message']);
        $this->assertSame($envelope, $record[1]['envelope']);
    }

    /**
     * @test
     */
    public function it_should_log_on_invoke()
    {
        $message  = new DefaultMessage('test', ['type' => 'event']);
        $envelope = new Envelope($message);
        $event    = new EnvelopeEvent($envelope, $this->getMock('Bernard\Queue'));

        $this->dispatcher->dispatch('bernard.invoke', $event);

        $this->assertArrayHasKey(LogLevel::INFO, $this->logger->records);
        $this->assertCount(1, $this->logger->records[LogLevel::INFO]);

        $record = $this->logger->records[LogLevel::INFO][0];

        $this->assertEquals('Invoking event from "test" queue', $record[0]);
        $this->assertSame($message, $record[1]['message']);
        $this->assertSame($envelope, $record[1]['envelope']);
    }

    /**
     * @test
     */
    public function it_should_log_on_reject()
    {
        $exception = new \RuntimeException();
        $message   = new DefaultMessage('test', ['type' => 'event']);
        $envelope  = new Envelope($message);
        $event     = new RejectEnvelopeEvent($envelope, $this->getMock('Bernard\Queue'), $exception);

        $this->dispatcher->dispatch('bernard.reject', $event);

        $this->assertArrayHasKey(LogLevel::ERROR, $this->logger->records);
        $this->assertCount(1, $this->logger->records[LogLevel::ERROR]);

        $record = $this->logger->records[LogLevel::ERROR][0];

        $this->assertEquals('Error processing event from "test" queue', $record[0]);
        $this->assertSame($message, $record[1]['message']);
        $this->assertSame($envelope, $record[1]['envelope']);
        $this->assertSame($exception, $record[1]['exception']);
    }
}

class DebugLogger extends AbstractLogger
{
    public $records = [];

    public function log($level, $message, array $context = [])
    {
        $this->records[$level][] = [$message, $context];
    }
}
