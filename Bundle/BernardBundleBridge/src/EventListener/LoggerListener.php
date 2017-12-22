<?php

namespace SimpleBus\BernardBundleBridge\EventListener;

use Bernard\Event\EnvelopeEvent;
use Bernard\Event\RejectEnvelopeEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LoggerListener implements EventSubscriberInterface
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public static function getSubscribedEvents()
    {
        return [
            'bernard.produce' => ['onProduce'],
            'bernard.invoke' => ['onInvoke'],
            'bernard.reject' => ['onReject'],
        ];
    }

    public function onProduce(EnvelopeEvent $event)
    {
        /* @var \Bernard\Message\PlainMessage $message */
        $message = $event->getEnvelope()->getMessage();

        $this->logger->info(sprintf('Produced %s into "%s" queue', $message->get('type'), $message->getName()), [
            'message' => $message,
            'envelope' => $event->getEnvelope(),
        ]);
    }

    public function onInvoke(EnvelopeEvent $event)
    {
        /* @var \Bernard\Message\PlainMessage $message */
        $message = $event->getEnvelope()->getMessage();

        $this->logger->info(sprintf('Invoking %s from "%s" queue', $message->get('type'), $message->getName()), [
            'message' => $message,
            'envelope' => $event->getEnvelope(),
        ]);
    }

    public function onReject(RejectEnvelopeEvent $event)
    {
        /* @var \Bernard\Message\PlainMessage $message */
        $message = $event->getEnvelope()->getMessage();

        $this->logger->error(sprintf('Error processing %s from "%s" queue', $message->get('type'), $message->getName()), [
            'message' => $message,
            'envelope' => $event->getEnvelope(),
            'exception' => $event->getException(),
        ]);
    }
}
