<?php

namespace SilverStripe\EventDispatcher\Tests\Listener\Form;

use SilverStripe\Admin\LeftAndMainFormRequestHandler;
use SilverStripe\CMS\Controllers\CMSMain;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\EventDispatcher\Dispatch\Dispatcher;
use SilverStripe\EventDispatcher\Dispatch\EventDispatcherInterface;
use SilverStripe\EventDispatcher\Event\EventContextInterface;
use SilverStripe\EventDispatcher\Tests\Listener\CMSMain\CMSMainFake;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FormAction;

class ListenerTest extends SapphireTest
{
    public function testListener()
    {
        $form = Form::create(
            CMSMainFake::create(),
            FieldList::create(),
            FieldList::create(
                $action = FormAction::create('myFormAction', 'My form action')
            )
        )->disableSecurityToken();

        $req = new HTTPRequest(
            'POST',
            '',
            [],
            [$action->getName() => $action->getName()]
        );

        $dispatcherMock = $this->getMockBuilder(Dispatcher::class)
            ->setConstructorArgs([
                $this->createMock(EventDispatcherInterface::class)
            ])
            ->setMethods(['trigger'])
            ->getMock();
        $dispatcherMock->expects($this->once())
            ->method('trigger')
            ->with(
                $this->equalTo('formSubmitted'),
                $this->callback(function($arg) use ($form, $req) {
                    $this->assertInstanceOf(EventContextInterface::class, $arg);
                    $this->assertEquals('myFormAction', $arg->getAction());
                    $this->assertEquals($form, $arg->get('form'));
                    $this->assertEquals($req, $arg->get('request'));
                    $this->assertEquals($req->postVars(), $arg->get('vars'));

                    return true;
                })
            );
        Injector::inst()->registerService($dispatcherMock, Dispatcher::class);

        $requestHandler = LeftAndMainFormRequestHandler::create($form);
        $requestHandler->httpSubmission($req);

    }
}
