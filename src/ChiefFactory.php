<?php

namespace Chief;

class ChiefFactory
{
    /**
     * @param array|Decorator[] $decorators
     * @return Chief
     */
    public static function createWithDecorators(array $decorators): Chief
    {
        return new Chief(new Executor, $decorators);
    }

    /**
     * @return Chief
     */
    public static function create(): Chief
    {
        return new Chief(new Executor);
    }
}
