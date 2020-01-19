<?php

namespace SilverStripe\EventDispatcher\Tests\Dispatch;

use SilverStripe\Dev\SapphireTest;
use SilverStripe\EventDispatcher\Dispatch\Dispatcher;
use SilverStripe\EventDispatcher\Dispatch\DispatcherLoaderInterface;
use SilverStripe\EventDispatcher\Dispatch\EventDispatcherInterface;
use SilverStripe\EventDispatcher\Event\EventContextInterface;
use SilverStripe\EventDispatcher\Event\EventHandlerInterface;
use InvalidArgumentException;

class DispatchTest extends SapphireTest
{
    public function testDispatcherHandlers()
    {
        $dispatcher = new Dispatcher($backend = new MockBackend());
        $dispatcher->setHandlers($this->buildHandlers());
        $dispatcher->trigger('notAnEvent', $e = new MockEvent());
        $this->assertEmpty($e->result);

        // No action, no trigger
        $dispatcher->trigger('myEvent1', $e = new MockEvent());
        $this->assertEmpty($e->result);

        $dispatcher->trigger('myEvent1', $e = new MockEvent('action'));
        $this->assertEquals('handler-onehandler-three', $e->result);

        $dispatcher->trigger('myEvent2', $e = new MockEvent('action'));
        $this->assertEquals('handler-one', $e->result);

        $dispatcher->trigger('myEvent3', $e = new MockEvent('action'));
        $this->assertEquals('handler-two', $e->result);

        // No matching event action falls back on main event (same as myEvent1)
        $dispatcher->trigger('myEvent1', $e = new MockEvent('nothing'));
        $this->assertEquals('handler-onehandler-three', $e->result);

        $dispatcher->trigger('myEvent1', $e = new MockEvent('test'));
        $this->assertEquals('handler-fourhandler-onehandler-three', $e->result);

        $dispatcher->trigger('myEvent3', $e = new MockEvent('test'));
        $this->assertEquals('handler-two', $e->result);
    }

    public function testHandlerException()
    {
        $dispatcher = new Dispatcher($backend = new MockBackend());
        $this->expectException(InvalidArgumentException::class);
        $dispatcher->setHandlers([
            [
                'on' => [],
            ],
            [
                'handler' => 'handler'
            ]
        ]);
    }

    public function testLoaders()
    {
        $dispatcher = new Dispatcher($backend = new MockBackend());
        $dispatcher->setHandlers($this->buildHandlers());
        $dispatcher->setLoaders([
            new class implements DispatcherLoaderInterface {
                public function addToDispatcher(EventDispatcherInterface $dispatcher): void
                {
                    $dispatcher->addListener('myEvent1', new class implements EventHandlerInterface {
                        public function fire(EventContextInterface $context): void
                        {
                            $context->result .= 'handler-five';
                        }
                    });
                }
            }
        ]);

        $dispatcher->trigger('myEvent1', $e = new MockEvent('test'));
        $this->assertEquals('handler-fourhandler-onehandler-threehandler-five', $e->result);
    }

    public function testLoaderException()
    {
        $dispatcher = new Dispatcher($backend = new MockBackend());
        $this->expectException(InvalidArgumentException::class);
        $dispatcher->setLoaders([
            new class implements EventHandlerInterface {
                public function fire(EventContextInterface $context): void
                {
                }
            }
        ]);
    }

    private function buildHandlers()
    {
        return [
            [
                'on' => ['myEvent1', 'myEvent2'],
                'handler' => new class implements EventHandlerInterface {
                    public function fire(EventContextInterface $context): void
                    {
                        $context->result .= 'handler-one';
                    }
                },
            ],
            [
                'on' => ['myEvent3'],
                'off' => ['myEvent1'],
                'handler' => new class implements EventHandlerInterface {
                    public function fire(EventContextInterface $context): void
                    {
                        $context->result .= 'handler-two';
                    }
                },
            ],
            [
                'on' => ['myEvent1'],
                'off' => ['myEvent2'],
                'handler' => new class implements EventHandlerInterface {
                    public function fire(EventContextInterface $context): void
                    {
                        $context->result .= 'handler-three';
                    }
                },
            ],
            [
                'on' => ['myEvent1.test', 'myEvent2.test', 'myEvent3.test'],
                'off' => ['myEvent3.test'],
                'handler' => new class implements EventHandlerInterface {
                    public function fire(EventContextInterface $context): void
                    {
                        $context->result .= 'handler-four';
                    }
                }
            ]
        ];
    }
}
