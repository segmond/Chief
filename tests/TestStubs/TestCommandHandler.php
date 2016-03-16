<?php

namespace Chief\TestStubs;

use Chief\Command;
use Chief\Handler;

class TestCommandHandler implements Handler
{
    /**
     * Handle a command execution
     *
     * @param Command $command
     * @return mixed
     */
    public function handle($command)
    {
        $command->handled = true;
    }
}
