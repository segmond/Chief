<?php

namespace Chief\Handlers;

use Chief\Command;
use Chief\Handler;
use Interop\Container\ContainerInterface;

class LazyLoadingHandler implements Handler
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var string
     */
    protected $handlerName;

    /**
     * @param string $handlerName
     * @param ContainerInterface $container
     */
    public function __construct($handlerName, ContainerInterface $container)
    {
        $this->container = $container;
        $this->handlerName = $handlerName;
    }

    /**
     * Handle a command execution
     *
     * @param $command
     * @return mixed
     */
    public function handle($command)
    {
        $handler = $this->container->get($this->handlerName);

        return $handler->handle($command);
    }
}
