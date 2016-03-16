<?php

namespace Chief;

use Chief\Resolvers\NativeCommandHandlerResolver;

class Executor implements CommandBus
{
    protected $resolver;

    public function __construct(CommandHandlerResolver $resolver = null)
    {
        $this->resolver = $resolver ?: new NativeCommandHandlerResolver;
    }

    /**
     * Execute a command
     *
     * @param Command $command
     * @return mixed
     */
    public function execute(Command $command)
    {
        $handler = $this->resolver->resolve($command);

        return $handler->handle($command);
    }
}
