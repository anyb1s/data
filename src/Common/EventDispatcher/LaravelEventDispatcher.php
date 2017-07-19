<?php

namespace AnyB1s\Data\Common\EventDispatcher;

class LaravelEventDispatcher implements EventDispatcher
{
    private $dispatcher;

    public function __construct(\Illuminate\Contracts\Events\Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function dispatch($event)
    {
        $this->dispatcher->dispatch($event);
    }

    public function subscribe($subscriber)
    {
        $this->dispatcher->subscribe($subscriber);
    }
}