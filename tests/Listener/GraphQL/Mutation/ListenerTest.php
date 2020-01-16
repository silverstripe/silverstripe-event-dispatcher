<?php

namespace SilverStripe\EventDispatcher\Tests\Listener\GraphQL\Mutation;

use GraphQL\Type\Definition\ResolveInfo;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\EventDispatcher\Dispatch\Dispatcher;
use SilverStripe\EventDispatcher\Dispatch\EventDispatcherInterface;
use SilverStripe\EventDispatcher\Event\EventContextInterface;
use SilverStripe\EventDispatcher\Listener\GraphQL\Mutation\Listener;
use SilverStripe\GraphQL\Scaffolding\Scaffolders\CRUD\Create;
use SilverStripe\GraphQL\Scaffolding\Scaffolders\CRUD\Delete;
use SilverStripe\GraphQL\Scaffolding\Scaffolders\CRUD\Update;
use SilverStripe\GraphQL\Tests\Fake\DataObjectFake;
use SilverStripe\Security\Member;

class ListenerTest extends SapphireTest
{
    protected $usesDatabase = true;

    protected static $extra_dataobjects = [
        DataObjectFake::class,
    ];

    public function testListener()
    {
        $context = ['currentUser' => new Member()];
        $info = new ResolveInfo([]);

        $dispatcherMock = $this->getMockBuilder(Dispatcher::class)
            ->setConstructorArgs([
                $this->createMock(EventDispatcherInterface::class)
            ])
            ->setMethods(['trigger'])
            ->getMock();
        $dispatcherMock->expects($this->exactly(3))
            ->method('trigger')
            ->withConsecutive(
                [
                    $this->equalTo('graphqlMutation'),
                    $this->callback(function ($arg) use ($context, $info) {
                        $this->assertInstanceOf(EventContextInterface::class, $arg);
                        $this->assertEquals(Listener::TYPE_CREATE, $arg->getAction());
                        $this->assertNull($arg->get('list'));
                        $this->assertInstanceOf(DataObjectFake::class, $arg->get('record'));
                        $this->assertArrayHasKey('Input', $arg->get('args'));
                        $this->assertEquals($context, $arg->get('context'));
                        $this->assertEquals($info, $arg->get('info'));

                        return true;
                    })
                ],
                [
                $this->equalTo('graphqlMutation'),
                    $this->callback(function ($arg) use ($context, $info) {
                        $this->assertInstanceOf(EventContextInterface::class, $arg);
                        $this->assertEquals(Listener::TYPE_UPDATE, $arg->getAction());
                        $this->assertNull($arg->get('list'));
                        $this->assertInstanceOf(DataObjectFake::class, $arg->get('record'));
                        $this->assertArrayHasKey('Input', $arg->get('args'));
                        $this->assertEquals($context, $arg->get('context'));
                        $this->assertEquals($info, $arg->get('info'));

                        return true;
                    })
                ],
                [
                    $this->equalTo('graphqlMutation'),
                    $this->callback(function ($arg) use ($context, $info) {
                        $this->assertInstanceOf(EventContextInterface::class, $arg);
                        $this->assertEquals(Listener::TYPE_DELETE, $arg->getAction());
                        $this->assertCount(1, $arg->get('list'));
                        $this->assertNull($arg->get('record'));
                        $this->assertArrayHasKey('IDs', $arg->get('args'));
                        $this->assertEquals($context, $arg->get('context'));
                        $this->assertEquals($info, $arg->get('info'));

                        return true;
                    })
                ]

            );
        Injector::inst()->registerService($dispatcherMock, Dispatcher::class);

        $create = new Create(DataObjectFake::class);
        $obj = $create->resolve(
            null,
            [
                'Input' => [
                    'MyField' => 'test'
                ]
            ],
            $context,
            $info
        );
        $update = new Update(DataObjectFake::class);
        $obj = $update->resolve(
            null,
            [
                'Input' => [
                    'ID' => $obj->ID,
                    'MyField' => 'test2'
                ]
            ],
            $context,
            $info
        );
        $delete = new Delete(DataObjectFake::class);
        $delete->resolve(
            null,
            ['IDs' => [$obj->ID]],
            $context,
            $info
        );

    }
}
