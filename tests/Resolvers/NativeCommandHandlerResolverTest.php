<?php

namespace Chief\Resolvers;

use Chief\ChiefTestCase;
use Chief\Command;
use Chief\Handler;
use Chief\CommandHandlerResolver;
use Chief\TestStubs\TestCommand;
use Chief\TestStubs\TestCommandHandler;
use Chief\TestStubs\TestCommandWithNestedHandler;
use Chief\TestStubs\Handlers\TestCommandWithNestedHandlerHandler;
use Chief\TestStubs\TestCommandWithoutHandler;

class NativeCommandHandlerResolverTest extends ChiefTestCase
{
    public function testInstantiable()
    {
        $this->assertTrue(new NativeCommandHandlerResolver instanceof CommandHandlerResolver);
    }

    public function testResolveThrowsExceptionWhenNoHandlerFound()
    {
        $resolver = new NativeCommandHandlerResolver;
        $this->setExpectedException('Chief\Exceptions\UnresolvableCommandHandlerException');
        $resolver->resolve(new TestCommandWithoutHandler);
    }

    public function testResolveReturnsHandlerWhenNotBoundAndInSameNamespaceWithHandlerSuffix()
    {
        $resolver = new NativeCommandHandlerResolver;
        $handler = $resolver->resolve(new TestCommand);
        $this->assertTrue($handler instanceof TestCommandHandler);
    }

    public function testResolveReturnsHandlerWhenNotBoundAndHandlerNestedInHandlersNamespaceWithHandlerSuffix()
    {
        $resolver = new NativeCommandHandlerResolver;
        $handler = $resolver->resolve(new TestCommandWithNestedHandler);
        $this->assertTrue($handler instanceof TestCommandWithNestedHandlerHandler);
    }

    public function testResolveReturnsHandlerBoundByObject()
    {
        $handler = $this->getMock(\Chief\Handler::class);
        $resolver = new NativeCommandHandlerResolver;
        $resolver->bindHandler(\Chief\TestStubs\TestCommandWithoutHandler::class, $handler);
        $this->assertEquals($resolver->resolve(new TestCommandWithoutHandler), $handler);
    }

    public function testResolveReturnsHandlerBoundByCallable()
    {
        $resolver = new NativeCommandHandlerResolver;
        $proof = new \stdClass();
        $resolver->bindHandler('Chief\TestStubs\TestCommandWithoutHandler', function (Command $command) use ($proof) {
                $proof->handled = true;
        });
        $command = new TestCommandWithoutHandler;
        $handler = $resolver->resolve($command);
        $this->assertTrue($handler instanceof Handler);
        $handler->handle($command);
        $this->assertEquals($proof->handled, true);
    }

    public function testResolveReturnsHandlerBoundByString()
    {
        $resolver = new NativeCommandHandlerResolver;
        $resolver->bindHandler('Chief\TestStubs\TestCommandWithoutHandler', 'Chief\TestStubs\TestCommandHandler');
        $command = new TestCommand;
        $handler = $resolver->resolve($command);
        $this->assertTrue($handler instanceof Handler);
        $handler->handle($command);
        $this->assertEquals($command->handled, true);
    }
}
