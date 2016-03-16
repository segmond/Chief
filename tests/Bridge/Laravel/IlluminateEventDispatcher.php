<?php

namespace Chief\Bridge\Laravel;

use Chief\ChiefTestCase;
use Chief\TestStubs\TestCommand;

class IlluminateEventDispatcherTest extends ChiefTestCase
{
    public function testDispatchHitsDispatcher()
    {
        $instance = new IlluminateEventDispatcher($dispatcher = $this->getMock(\Illuminate\Events\Dispatcher::class));
        $eventName = 'Foo.Event';
        $eventdata = new TestCommand();
        $dispatcher->expects($this->once())->method('fire')->with($eventName, $eventdata);
        $instance->dispatch($eventName, $eventdata);
    }
}
