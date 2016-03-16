<?php

namespace Chief\TestStubs;

use Chief\Command;
use Chief\Handler;

class SelfHandlingCommand implements Command, Handler
{
    public function handle($command)
    {
        $command->handled = true;
    }
}
