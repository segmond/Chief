<?php

namespace Chief\Transaction;

use Chief\Executor;
use Chief\ChiefTestCase;
use Chief\CommandBus;
use Chief\Resolvers\NativeCommandHandlerResolver;
use Chief\TestStubs\TestCommand;
use Chief\TestStubs\TestTransactionalCommand;

class TransactionalCommandLockingDecoratorTest extends ChiefTestCase
{
    public function testInstance()
    {
        $this->assertTrue(new TransactionalCommandLockingDecorator instanceof CommandBus);
    }

    public function testNestedCommandsExecutedAfterInitialCommand()
    {
        $syncBus = new Executor($resolver = new NativeCommandHandlerResolver());
        $bus = new TransactionalCommandLockingDecorator($syncBus);
        $command = new TestTransactionalCommand();

        $lastCalled = null;
        $resolver->bindHandler('Chief\TestStubs\TestCommand', function () use ($bus, &$lastCalled) {
            $lastCalled = 'TestCommand';
        });

        $resolver->bindHandler('Chief\TestStubs\TestTransactionalCommand', function () use ($bus, &$lastCalled) {
            $bus->execute(new TestCommand());
            $lastCalled = 'TestTransactionalCommand';
        });
        $bus->execute($command);

        $this->assertEquals($lastCalled, 'TestCommand');
    }

    public function testNestedCommandsRanWhenMultiple()
    {
        $syncBus = new Executor($resolver = new NativeCommandHandlerResolver());
        $bus = new TransactionalCommandLockingDecorator($syncBus);
        $command = new TestTransactionalCommand();

        $countTestCommandCalled = 0;
        $resolver->bindHandler('Chief\TestStubs\TestCommand', function () use ($bus, &$countTestCommandCalled) {
            $countTestCommandCalled++;
        });

        $resolver->bindHandler('Chief\TestStubs\TestTransactionalCommand', function () use ($bus, &$lastCalled) {
            $bus->execute(new TestCommand());
            $bus->execute(new TestCommand());
            $bus->execute(new TestCommand());
        });
        $bus->execute($command);

        $this->assertEquals($countTestCommandCalled, 3);
    }

    public function testNestedCommandsNotExecutedWhenInitialCommandFailsBeforeReturning()
    {
        $syncBus = new Executor($resolver = new NativeCommandHandlerResolver());
        $bus = new TransactionalCommandLockingDecorator($syncBus);
        $command = new TestTransactionalCommand();

        $countTestCommandCalled = 0;
        $resolver->bindHandler('Chief\TestStubs\TestCommand', function () use ($bus, &$countTestCommandCalled) {
            $countTestCommandCalled++;
        });

        $resolver->bindHandler('Chief\TestStubs\TestTransactionalCommand', function () use ($bus, &$lastCalled) {
            $bus->execute(new TestCommand());
            $bus->execute(new TestCommand());
            $bus->execute(new TestCommand());
            throw new \Exception('Something failed');
        });

        $this->setExpectedException('Exception');
        $bus->execute($command);

        $this->assertEquals($countTestCommandCalled, 0);
    }
}
