<?php

namespace SilverStripe\EventDispatcher\Dispatch;

use SilverStripe\EventDispatcher\Event\EventHandlerInterface;
use Psr\EventDispatcher\EventDispatcherInterface as PsrEventDispatcherInterface;

interface EventDispatcherInterface extends PsrEventDispatcherInterface
{
    /**
     * @param string $eventName
     * @param EventHandlerInterface $listener
     * @return $this
     */
    public function addListener(string $eventName, EventHandlerInterface $listener): EventDispatcherInterface;

    /**
     * @param string $eventName
     * @param EventHandlerInterface $listener
     * @return $this
     */
    public function removeListener(string $eventName, EventHandlerInterface $listener): EventDispatcherInterface;

    /**
     * @param object $eventContext
     * @param string|null $eventName
     */
    public function dispatch(object $eventContext, string $eventName = null): void;
}
