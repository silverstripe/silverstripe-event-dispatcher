<?php

namespace SilverStripe\EventDispatcher\Tests\Listener\GridField\Action;

use SilverStripe\Control\HTTPRequest;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\EventDispatcher\Dispatch\Dispatcher;
use SilverStripe\EventDispatcher\Dispatch\EventDispatcherInterface;
use SilverStripe\EventDispatcher\Event\EventContextInterface;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\ORM\ArrayList;

class ListenerTest extends SapphireTest
{
    public function testListener()
    {
        $req = new HTTPRequest(
            'POST',
            'mygrid',
            [],
            ['var' => 'value']
        );

        $grid = GridField::create(
            'myGrid',
            'My grid',
            ArrayList::create(),
            $config = new GridFieldConfig()
        );

        $config->addComponent(new URLHandlerFake());

        $dispatcherMock = $this->getMockBuilder(Dispatcher::class)
            ->setConstructorArgs([
                $this->createMock(EventDispatcherInterface::class)
            ])
            ->setMethods(['trigger'])
            ->getMock();
        $dispatcherMock->expects($this->once())
            ->method('trigger')
            ->with(
                $this->equalTo('gridFieldAction'),
                $this->callback(function($arg) use ($grid, $req) {
                    $this->assertInstanceOf(EventContextInterface::class, $arg);
                    $this->assertEquals('handleMyGrid', $arg->getAction());
                    $this->assertEquals($req, $arg->get('request'));
                    $this->assertEquals('my grid success', $arg->get('result'));
                    $this->assertEquals($grid, $arg->get('gridField'));

                    return true;
                })
            );
        Injector::inst()->registerService($dispatcherMock, Dispatcher::class);

        $grid->handleRequest($req);
    }
}
