<?php

namespace Chief\TestStubs;

use Chief\Command;
use Chief\Handler;

class NonInterfaceImplementingCommandHandler
{
    public function handle($command)
    {
        $command->handled = true;
    }
}
