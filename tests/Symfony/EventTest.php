<?php

namespace SilverStripe\EventDispatcher\Tests\Symfony;

use SilverStripe\Dev\SapphireTest;
use SilverStripe\EventDispatcher\Symfony\Event;

class EventTest extends SapphireTest
{
    public function testEvent()
    {
        $event = new Event('myAction', ['myProp' => 'myValue', 'boolProp' => false]);
        $this->assertEquals('myAction', $event->getAction());
        $this->assertEquals('myValue', $event->get('myProp'));
        $this->assertFalse($event->get('boolProp'));
        $this->assertNull($event->get('notAProp'));
    }
}
