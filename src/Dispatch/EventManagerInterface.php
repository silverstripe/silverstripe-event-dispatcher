<?php


namespace SilverStripe\EventDispatcher\Dispatch;

use SilverStripe\EventDispatcher\Event\EventContextInterface;
use SilverStripe\EventDispatcher\Event\EventHandlerInterface;

interface EventManagerInterface
{
    /**
     * @param string $eventName
     * @param EventHandlerInterface $listener
     * @return $this
     */
    public function addListener(string $eventName, EventHandlerInterface $listener): self;

    /**
     * @param string $eventName
     * @param EventHandlerInterface $listener
     * @return $this
     */
    public function removeListener(string $eventName, EventHandlerInterface $listener): self;

    /**
     * @param string $eventName
     * @param EventContextInterface $context
     */
    public function trigger(string $eventName, EventContextInterface $context): void;
}
