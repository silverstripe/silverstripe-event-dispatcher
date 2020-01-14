<?php


namespace SilverStripe\EventDispatcher\Symfony;

use SilverStripe\EventDispatcher\Dispatch\EventManagerInterface;
use SilverStripe\EventDispatcher\Event\EventContextInterface;
use SilverStripe\EventDispatcher\Event\EventHandlerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

class Backend implements EventManagerInterface
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
    ): EventManagerInterface
    {
        $this->eventDispatcher->addListener($eventName, [$listener, 'fire']);

        return $this;
    }

    /**
     * @param string $eventName
     * @param EventHandlerInterface $listener
     * @return EventManagerInterface
     */
    public function removeListener(
        string $eventName,
        EventHandlerInterface $listener
    ): EventManagerInterface
    {
        $this->eventDispatcher->removeListener($eventName, [$listener, 'fire']);

        return $this;
    }

    /**
     * @param string $eventName
     * @param EventContextInterface $context
     */
    public function trigger(string $eventName, EventContextInterface $context): void
    {
        $this->eventDispatcher->dispatch($context, $eventName);
    }
}
