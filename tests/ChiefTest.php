<?php

namespace Chief;

use Chief\Executor;
use Chief\Logging\LoggingDecorator;
use Chief\Resolvers\NativeCommandHandlerResolver;
use Chief\TestStubs\LogDecoratorCommandBus;
use Chief\TestStubs\NonInterfaceImplementingCommand;
use Chief\TestStubs\SelfHandlingCommand;
use Chief\TestStubs\TestCommand;
use Chief\TestStubs\TestCommandWithoutHandler;

class ChiefTest extends ChiefTestCase
{
    public function testInstantiable()
    {
        $this->assertTrue(new Chief instanceof CommandBus);
    }

    public function testExecuteFiresByAutoResolution()
    {
        $bus = new Chief();
        $command = new TestCommand;
        $bus->execute($command);
        $this->assertEquals($command->handled, true);
    }

    public function testExecuteFiresHandlerAttachedByInstance()
    {
        $resolver = new NativeCommandHandlerResolver;
        $resolver->bindHandler(\Chief\TestStubs\TestCommand::class, $handler = $this->getMock(\Chief\Handler::class));
        $syncBus = new Executor($resolver);
        $bus = new Chief($syncBus);
        $command = new TestCommand;
        $handler->expects($this->once())->method('handle')->with($command);
        $bus->execute($command);
    }

    public function testExecuteFiresHandlerAttachedByCallable()
    {
        $resolver = new NativeCommandHandlerResolver;
        $resolver->bindHandler('Chief\TestStubs\TestCommand', function (Command $command) {
                $command->handled = true;
        });
        $bus = new Chief(new Executor($resolver));
        $command = new TestCommand;
        $bus->execute($command);
        $this->assertEquals($command->handled, true);
    }

    public function testExecuteFiresHandlerAttachedByString()
    {
        $resolver = new NativeCommandHandlerResolver;
        $resolver->bindHandler('Chief\TestStubs\TestCommand', 'Chief\TestStubs\TestCommandHandler');
        $bus = new Chief(new Executor($resolver));
        $command = new TestCommand;
        $bus->execute($command);
        $this->assertEquals($command->handled, true);
    }

    public function testExecuteThrowsExceptionWhenNoHandler()
    {
        $bus = new Chief();
        $command = new TestCommandWithoutHandler;
        $this->setExpectedException('Exception');
        $bus->execute($command);
    }

    public function testCommandCanHandleItselfIfImplementsCommandHandler()
    {
        $bus = new Chief();
        $command = $this->getMock(\Chief\TestStubs\SelfHandlingCommand::class);
        $command->expects($this->once())->method('handle')->with($command);
        $bus->execute($command);
    }

    public function testDecoratorCommandBus()
    {
        $bus = new LogDecoratorCommandBus(
            $logger = $this->getMock(\Psr\Log\LoggerInterface::class),
            $innerBus = $this->getMock(\Chief\Executor::class)
        );
        $chief = new Chief($bus);
        $command = new TestCommand;
        $logger->expects($this->exactly(2))->method('info');
        $innerBus->expects($this->once())->method('execute')->with($command);
        $chief->execute($command);
    }

    public function testInstanceWithDecorators()
    {
        $chief = new Chief(new Executor, [
            $decorator = $this->getMock(\Chief\Decorator::class)
        ]);
        $command = new TestCommand;
        $decorator->expects($this->once())->method('execute')->with($command);
        $chief->execute($command);
    }

    public function testInstanceWithMultipleDecoratorsHitsNestedDecorators()
    {
        $logger = $this->getMock(\Psr\Log\LoggerInterface::class);

        $chief = new Chief(new Executor, [
            $decoratorOne = new LoggingDecorator($logger),
            $decoratorTwo = $this->getMock(\Chief\Decorator::class),
        ]);
        $command = new TestCommand;
        $decoratorTwo->expects($this->once())->method('execute')->with($command);
        $chief->execute($command);
    }

    public function testInstanceWithMultipleDecoratorsHitsHandler()
    {
        $logger = $this->getMock(\Psr\Log\LoggerInterface::class);

        $chief = new Chief(new Executor, [
            $decoratorOne = new LoggingDecorator($logger),
            $decoratorTwo = new LoggingDecorator($logger),
        ]);
        $command = new SelfHandlingCommand;
        $chief->execute($command);
        $this->assertEquals($command->handled, true);
    }
    public function testInnerBusResponseIsReturnedByChief()
    {
        $chief = new Chief($bus = $this->getMock(\Chief\CommandBus::class));
        $bus->expects($this->once())->method('execute')->willReturn('foo-bar');
        $response = $chief->execute(new TestCommand);
        $this->assertEquals($response, 'foo-bar');
    }

    public function testExecuteWithHandlerWhichDoesNotImplementInterface()
    {
        $command = new NonInterfaceImplementingCommand();
        $chief = new Chief();
        $chief->execute($command);
        $this->assertTrue($command->handled);
    }
}
