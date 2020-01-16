<?php

namespace SilverStripe\EventDispatcher\Tests\Listener\GridField\Alteration;

use SilverStripe\Control\HTTPRequest;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\EventDispatcher\Dispatch\Dispatcher;
use SilverStripe\EventDispatcher\Dispatch\EventDispatcherInterface;
use SilverStripe\EventDispatcher\Event\EventContextInterface;
use SilverStripe\EventDispatcher\Tests\Listener\CMSMain\CMSMainFake;
use SilverStripe\EventDispatcher\Tests\Listener\GridField\Action\URLHandlerFake;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridField_FormAction;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\ORM\ArrayList;

class ListenerTest extends SapphireTest
{
    public function testListener()
    {
        $grid = GridField::create(
            'myGrid',
            'My grid',
            ArrayList::create(),
            $config = new GridFieldConfig()
        );

        $form = Form::create(
            CMSMainFake::create(),
            FieldList::create(),
            FieldList::create()
        );

        $grid->setForm($form);

        $formAction = new GridField_FormAction(
            $grid,
            'myCustomAction',
            'Custom Action',
            'myaction',
            $args = ['myarg' => 'myvalue']
        );
        $token = $form->getSecurityToken();
        $req = new HTTPRequest(
            'POST',
            '',
            [],
            [
                'SecurityID' => $token->getValue(),
                $formAction->getAttributes()['name'] => $formAction->getName()
            ]
        );


        $config->addComponent(new ActionProviderFake());

        $dispatcherMock = $this->getMockBuilder(Dispatcher::class)
            ->setConstructorArgs([
                $this->createMock(EventDispatcherInterface::class)
            ])
            ->setMethods(['trigger'])
            ->getMock();
        $dispatcherMock->expects($this->once())
            ->method('trigger')
            ->with(
                $this->equalTo('gridFieldAlteration'),
                $this->callback(function($arg) use ($grid, $req, $args) {
                    $this->assertInstanceOf(EventContextInterface::class, $arg);
                    $this->assertEquals('myaction', $arg->getAction());
                    $this->assertEquals($req, $arg->get('request'));
                    $this->assertEquals('my action success', $arg->get('result'));
                    $this->assertEquals($grid, $arg->get('gridField'));
                    $this->assertEquals($args, $arg->get('args'));

                    return true;
                })
            );
        Injector::inst()->registerService($dispatcherMock, Dispatcher::class);

        $grid->handleRequest($req);

    }
}
