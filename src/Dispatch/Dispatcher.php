<?php


namespace SilverStripe\EventDispatcher\Dispatch;

use InvalidArgumentException;
use Exception;
use SilverStripe\Core\Injector\Injectable;
use SilverStripe\EventDispatcher\Event\EventContextInterface;
use SilverStripe\EventDispatcher\Event\EventHandlerInterface;

class Dispatcher
{
    use Injectable;

    /**
     * @var EventManagerInterface
     */
    private $backend;

    /**
     * @var EventHandlerInterface[]
     */
    private $handlers = [];

    /**
     * @var DispatcherLoaderInterface[]
     */
    private $loaders = [];

    /**
     * @var bool
     */
    private $initialised = false;

    /**
     * Dispatcher constructor.
     * @param EventManagerInterface $backend
     */
    public function __construct(EventManagerInterface $backend)
    {
        $this->backend = $backend;
    }

    /**
     * @param DispatcherLoaderInterface[] $loaders
     * @return $this
     */
    public function setLoaders($loaders = [])
    {
        foreach ($loaders as $loader) {
            if (!$loader instanceof DispatcherLoaderInterface) {
                throw new InvalidArgumentException(sprintf(
                    '%s not passed an instance of %s',
                    __CLASS__,
                    DispatcherLoaderInterface::class
                ));
            }
        }
        $this->loaders = $loaders;

        return $this;
    }

    /**
     * @param array $handlers
     * @throws Exception
     */
    public function setHandlers(array $handlers)
    {
        foreach ($handlers as $spec) {
            if (!isset($spec['handler']) || !isset($spec['on'])) {
                throw new InvalidArgumentException('Event handlers must have a "on" and "handler" nodes');
            }
            $on = is_array($spec['on']) ? $spec['on'] : [$spec['on']];
            $handler = $spec['handler'];

            if (!$handler instanceof EventHandlerInterface) {
                throw new InvalidArgumentException(sprintf(
                    'Handler for %s is not an instance of %s',
                    implode(', ', $on),
                    EventHandlerInterface::class
                ));
            }

            foreach ($on as $eventName => $shouldInclude) {
                if ($shouldInclude) {
                    $this->backend->addListener($eventName, $handler);
                }
            }
        }
    }

    /**
     * @param string $event
     * @param EventContextInterface $context
     */
    public function trigger(string $event, EventContextInterface $context): void
    {
        if (!$this->initialised) {
            $this->initialise();
        }

        $action = $context->getAction();
        if ($action === null) {
            return;
        }

        // First fire listeners to <eventName.actionName>, then just fire generic <eventName> listeners
        $eventsToFire = [ $event . '.' . $action, $event];
        foreach ($eventsToFire as $event) {
            $handlers = $this->handlers[$event] ?? [];
            /* @var EventHandlerInterface $handler */
            foreach ($handlers as $handler) {
                $handler->fire($context);
            }
        }
    }

    private function initialise(): void
    {
        if ($this->initialised) {
            return;
        }

        foreach ($this->loaders as $loader) {
            $loader->addToDispatcher($this);
        }
        $this->initialised = true;
    }

}
