<?php

namespace Chief;

/**
 * The main Chief class is a CommandBus, which is effectively a decorator
 * around another CommandBus interface
 *
 */
class Chief implements CommandBus
{
    /**
     * @var CommandBus
     */
    protected $bus;

    /**
     * Constructor
     *
     * @param CommandBus $executor
     * @param array $decorators Array of \Chief\Decorator objects
     */
    public function __construct(CommandBus $executor = null, array $decorators = [])
    {
        $this->bus = $executor ?: new Executor;

        foreach ($decorators as $decorator) {
            $this->pushDecorator($decorator);
        }
    }

    /**
     * Push a new Decorator on to the stack
     * @param Decorator $decorator
     */
    public function pushDecorator(Decorator $decorator)
    {
        $decorator->setInnerBus($this->bus);
        $this->bus = $decorator;
    }

    /**
     * Execute a command
     *
     * @param Command $command
     * @throws \InvalidArgumentException
     * @return mixed
     */
    public function execute(Command $command)
    {
        return $this->bus->execute($command);
    }
}
