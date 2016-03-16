<?php

namespace Chief\Handlers;

use Chief\Command;
use Chief\Handler;

class CallableHandler implements Handler
{
    /**
     * @var callable
     */
    protected $handler;

    public function __construct(callable $handler)
    {
        $this->handler = $handler;
    }

    /**
     * Handle a command execution
     *
     * @param $command
     * @return mixed
     */
    public function handle($command)
    {
        $callableHandler = $this->handler;
        return $callableHandler($command);
    }
}
