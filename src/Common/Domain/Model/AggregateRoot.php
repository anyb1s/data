<?php

namespace AnyB1s\Data\Domain\Model;

use Assert\Assert;

/**
 * Aggregate roots can use this trait
 */
trait AggregateRoot
{
    private $events = [];

    /**
     * Keep track of
     *
     * @param object $event
     */
    protected function recordThat($event)
    {
        Assert::that($event)
            ->isObject($event, 'An event should be an object');

        $this->events[] = $event;
    }

    /**
     * @return object[]
     */
    final public function recordedEvents()
    {
        return $this->events;
    }

    public function clearEvents(): void
    {
        $this->events = [];
    }
}