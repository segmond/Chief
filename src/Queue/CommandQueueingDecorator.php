<?php

namespace Chief\Queue;

use Chief\Executor;
use Chief\Command;
use Chief\CommandBus;
use Chief\Decorators\InnerBusTrait;
use Chief\Decorator;

/**
 * Queue commands which implement QueueableCommand into a CommandQueuer
 */
class CommandQueueingDecorator implements Decorator
{
    use InnerBusTrait;

    /**
     * @var \Chief\Queue\CommandQueuer
     */
    protected $queuer;

    /**
     * @param CommandQueuer $queuer
     * @param CommandBus $innerCommandBus
     */
    public function __construct(CommandQueuer $queuer, CommandBus $innerCommandBus = null)
    {
        $this->queuer = $queuer;
        $this->setInnerBus($innerCommandBus ?: new Executor());
    }

    /**
     * Execute a command
     *
     * @param Command $command
     * @return void|mixed
     */
    public function execute(Command $command)
    {
        if ($command instanceof QueueableCommand) {
            $this->queuer->queue($command);
            return null;
        }

        return $this->innerCommandBus->execute($command);
    }
}
