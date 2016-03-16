<?php

namespace Chief\Bus;

use Chief\ChiefTestCase;
use Chief\CommandBus;
use Chief\Executor;
use Chief\TestStubs\TestCommand;

class SynchronousCommandBusTest extends ChiefTestCase
{
    public function testInstance()
    {
        $this->assertTrue(new Executor instanceof CommandBus);
    }

    public function testExecuteFiresHandlerProvidedByResolver()
    {
        $resolver = $this->getMock(\Chief\CommandHandlerResolver::class);
        $handler = $this->getMock(\Chief\Handler::class);
        $bus = new Executor($resolver);
        $command = new TestCommand;
        $handler->expects($this->once())->method('handle')->with($command);
        $resolver->expects($this->once())->method('resolve')->with($command)->willReturn($handler);
        $bus->execute($command);
    }

    public function testExecuteReturnsHandlerResponse()
    {
        $resolver = $this->getMock(\Chief\CommandHandlerResolver::class);
        $handler = $this->getMock(\Chief\Handler::class);
        $bus = new Executor($resolver);
        $command = new TestCommand;
        $handler->expects($this->once())->method('handle')->with($command)->willReturn('Foo-Bar.');
        $resolver->expects($this->once())->method('resolve')->with($command)->willReturn($handler);
        $response = $bus->execute($command);
        $this->assertEquals($response, 'Foo-Bar.');
    }
}
