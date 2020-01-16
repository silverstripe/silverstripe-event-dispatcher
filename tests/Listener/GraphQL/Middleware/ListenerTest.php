<?php

namespace SilverStripe\EventDispatcher\Tests\Listener\GraphQL\Middleware;

use SilverStripe\Core\Injector\Injector;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\EventDispatcher\Dispatch\Dispatcher;
use SilverStripe\EventDispatcher\Dispatch\EventDispatcherInterface;
use SilverStripe\EventDispatcher\Event\EventContextInterface;
use SilverStripe\GraphQL\Manager;
use SilverStripe\GraphQL\Tests\Fake\DataObjectFake;

class ListenerTest extends SapphireTest
{
    public function testListener()
    {
        $manager = Manager::create();
        $manager->applyConfig([
            'scaffolding' => [
                'types' => [
                    DataObjectFake::class => [
                        'fields' => '*',
                        'operations' => '*'
                    ]
                ]
            ]
        ]);

        $params = [ 'Param1' => 'Value1' ];

        $dispatcherMock = $this->getMockBuilder(Dispatcher::class)
            ->setConstructorArgs([
                $this->createMock(EventDispatcherInterface::class)
            ])
            ->setMethods(['trigger'])
            ->getMock();
        $dispatcherMock->expects($this->once())
            ->method('trigger')
            ->with(
                $this->equalTo('graphqlOperation'),
                $this->callback(function ($arg) use ($manager, $params) {
                    $this->assertInstanceOf(EventContextInterface::class, $arg);
                    $this->assertEquals('MyTestQuery', $arg->getAction());
                    $this->assertEquals($manager->schema(), $arg->get('schema'));
                    $this->assertEquals($params, $arg->get('params'));

                    return true;
                })
            );
        Injector::inst()->registerService($dispatcherMock, Dispatcher::class);
        $query = <<<GRAPHQL
query MyTestQuery (\$Var:String!) {
    readSomething {
        fieldOne
        fieldTwo
    }
}
GRAPHQL;
        $manager->queryAndReturnResult($query, $params);
    }
}
