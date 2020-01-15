<?php


namespace SilverStripe\EventDispatcher\Tests\Dispatch;


use SilverStripe\EventDispatcher\Dispatch\EventDispatcherInterface;
use SilverStripe\EventDispatcher\Event\EventHandlerInterface;

class MockBackend implements EventDispatcherInterface
{
    public $result = '';

    public $listeners = [];

    public function dispatch(object $eventContext, string $eventName = null): void
    {
        $listeners = $this->listeners[$eventName] ?? [];
        foreach ($listeners as $listener) {
            $listener->fire($eventContext);
        }
    }

    public function addListener(string $eventName, EventHandlerInterface $listener): EventDispatcherInterface
    {
        if (!isset($this->listeners[$eventName])) {
            $this->listeners[$eventName] = [];
        }
        $this->listeners[$eventName][] = $listener;

        return $this;
    }

    public function removeListener(string $eventName, EventHandlerInterface $listener): EventDispatcherInterface
    {

    }
}
