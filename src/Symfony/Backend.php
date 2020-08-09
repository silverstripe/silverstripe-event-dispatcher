<?php

namespace SilverStripe\EventDispatcher\Symfony;

use SilverStripe\EventDispatcher\Dispatch\EventDispatcherInterface;
use SilverStripe\EventDispatcher\Event\EventHandlerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

class Backend implements EventDispatcherInterface
{
    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * Backend constructor.
     */
    public function __construct()
    {
        $this->eventDispatcher = new EventDispatcher();
    }

    public function addListener(
        string $eventName,
        EventHandlerInterface $listener
    ): EventDispatcherInterface {
        $this->eventDispatcher->addListener($eventName, [$listener, 'fire']);

        return $this;
    }

    /**
     * @param string $eventName
     * @param EventHandlerInterface $listener
     * @return EventDispatcherInterface
     */
    public function removeListener(
        string $eventName,
        EventHandlerInterface $listener
    ): EventDispatcherInterface {
        $this->eventDispatcher->removeListener($eventName, [$listener, 'fire']);

        return $this;
    }

    /**
     * @param object $eventContext
     * @param string $eventName
     */
    public function dispatch(object $eventContext, ?string $eventName = null): void
    {
        $this->eventDispatcher->dispatch($eventContext, $eventName);
    }
}
