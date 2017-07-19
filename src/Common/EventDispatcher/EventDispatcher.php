<?php

namespace AnyB1s\Data\Common\EventDispatcher;

interface EventDispatcher
{
    public function dispatch($event);

    public function subscribe($subscriber);
}