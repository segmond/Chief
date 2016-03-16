<?php

namespace Chief\TestStubs\Handlers;

use Chief\Command;
use Chief\Handler;

class TestCommandWithNestedHandlerHandler implements Handler
{
    /**
     * Handle a command execution
     *
     * @param $command
     * @return mixed
     */
    public function handle($command)
    {
        $command->handled = true;
    }
}
