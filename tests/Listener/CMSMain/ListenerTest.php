<?php

namespace SilverStripe\EventDispatcher\Tests\Listener\CMSMain;

use SilverStripe\CMS\Controllers\CMSMain;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\Session;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\EventDispatcher\Dispatch\Dispatcher;
use SilverStripe\EventDispatcher\Dispatch\EventDispatcherInterface;
use SilverStripe\EventDispatcher\Event\EventContextInterface;

class ListenerTest extends SapphireTest
{
    public function testListener()
    {
        $dispatcherMock = $this->getMockBuilder(Dispatcher::class)
            ->setConstructorArgs([
                $this->createMock(EventDispatcherInterface::class)
            ])
            ->setMethods(['trigger'])
            ->getMock();
        $dispatcherMock->expects($this->once())
            ->method('trigger')
            ->with(
                $this->equalTo('cmsAction'),
                $this->callback(function($arg) {
                    $this->assertInstanceOf(EventContextInterface::class, $arg);
                    $this->assertEquals('myaction', $arg->getAction());
                    $this->assertEquals('this is my action', $arg->get('result'));
                    $this->assertEquals(CMSMain::config()->get('tree_class'), $arg->get('treeClass'));
                    $this->assertEquals('123', $arg->get('id'));

                    return true;
                })
            );
        Injector::inst()->registerService($dispatcherMock, Dispatcher::class);
        $this->logInWithPermission('ADMIN');
        $inst = CMSMainFake::create();
        $inst->handleRequest((new HTTPRequest(
            'GET',
            'myaction',
            ['ID' => 123]
        ))->setSession(new Session([])));
    }
}
