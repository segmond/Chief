<?php

namespace Chief\Container;

use Interop\Container\ContainerInterface;

class NativeContainer implements ContainerInterface
{
    public function has($id)
    {
        return class_exists($id);
    }

    public function get($id)
    {
        return new $id;
    }
}
