<?php

namespace Chief\Queue;

use Chief\CommandBus;
use Chief\DecoratorTestCase;
use Chief\TestStubs\TestCommand;
use Chief\TestStubs\TestQueueableCommand;

class CommandQueueingDecoratorTestCase extends DecoratorTestCase
{
    public function testInstance()
    {
        $this->assertTrue(new CommandQueueingDecorator($this->getMock(\Chief\Queue\CommandQueuer::class)) instanceof CommandBus);
    }

    public function testExecutePutsNormalCommandInInnerBus()
    {
        $queuer = $this->getMock(\Chief\Queue\CommandQueuer::class);
        $innerBus = $this->getMock(\Chief\CommandBus::class);
        $bus = new CommandQueueingDecorator($queuer, $innerBus);
        $command = new TestCommand;
        $queuer->expects($this->never())->method('queue');
        $innerBus->expects($this->once())->method('execute')->with($command);
        $bus->execute($command);
    }

    public function testExecutePutsQueueableCommandInQueuer()
    {
        $queuer = $this->getMock(\Chief\Queue\CommandQueuer::class);
        $bus = new CommandQueueingDecorator($queuer);
        $command = new TestQueueableCommand;
        $queuer->expects($this->once())->method('queue')->with($command);
        $bus->execute($command);
    }

    /**
     * @return \Chief\Decorator
     */
    protected function getDecorator()
    {
        return new CommandQueueingDecorator($this->getMock(\Chief\Queue\CommandQueuer::class));
    }


}
