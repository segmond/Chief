<?php

namespace Chief;

interface Handler
{
    /**
     * Handle a command execution
     *
     * @param $command
     * @return mixed
     */
    public function handle($command);
}
