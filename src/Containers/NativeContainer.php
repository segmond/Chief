<?php

namespace Chief\Containers;

use Chief\Container;
use Interop\Container\ContainerInterface;

class NativeContainer implements Container, ContainerInterface
{
    /**
     * Instantiate and return an object based on its class name
     *
     * @param $class
     * @return object
     */
    public function make($class)
    {
        return new $class;
    }

    public function has($id)
    {
        return class_exists($id);
    }

    public function get($id)
    {
        return $this->make($id);
    }
}
