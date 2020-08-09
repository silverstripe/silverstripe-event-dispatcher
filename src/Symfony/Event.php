<?php

namespace SilverStripe\EventDispatcher\Symfony;

use SilverStripe\Core\Injector\Injectable;
use SilverStripe\EventDispatcher\Event\EventContextInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class Event implements EventContextInterface
{
    use Injectable;

    /**
     * @var GenericEvent
     */
    private $event;

    /**
     * Event constructor.
     * @param string $action
     * @param array $properties
     */
    public function __construct(string $action = null, array $properties = [])
    {
        $this->event = new GenericEvent($action, $properties);
    }

    /**
     * @return string|null
     */
    public function getAction(): ?string
    {
        return $this->event->getSubject();
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public function get(string $name)
    {
        try {
            $arg = $this->event->getArgument($name);

            return $arg;
        } catch (\InvalidArgumentException $e) {
            return null;
        }
    }
}
