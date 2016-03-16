<?php

namespace Chief\Event;

use Chief\CommandBus;
use Chief\DecoratorTestCase;
use Chief\TestStubs\TestCommand;

class EventDispatchingDecoratorTestCase extends DecoratorTestCase
{
    public function testInstance()
    {
        $decorator = $this->getDecorator();
        $decorator->setInnerBus($this->getMock(\Chief\CommandBus::class));
        $this->assertTrue($decorator instanceof CommandBus);
    }

    public function testExecuteFiresEventAndInnerBus()
    {
        $decorator = new EventDispatchingDecorator(
            $dispatcher = $this->getMock(\Chief\Event\EventDispatcher::class)
        );
        $decorator->setInnerBus($bus = $this->getMock(\Chief\CommandBus::class));
        $command = new TestCommand();
        $bus->expects($this->once())->method('execute')->with($command);
        $dispatcher->expects($this->once())->method('dispatch')->with('Chief.TestStubs.TestCommand', [$command]);
        $decorator->execute($command);
    }

    protected function getDecorator()
    {
        return new EventDispatchingDecorator($this->getMock(\Chief\Event\EventDispatcher::class));
    }
}
