<?php

namespace Chief\Containers;

use Chief\ChiefTestCase;
use Chief\Container\NativeContainer;

class NativeContainerTest extends ChiefTestCase
{
    public function test_get_created_new_instances()
    {
        $container = new NativeContainer();
        $made = $container->get(\stdClass::class);
        $this->assertTrue($made instanceof \stdClass);
    }
}
