<?php

namespace AnyB1s\Data\Common\EventSourcing;

/**
 * Interface EventSourcedAggregate
 * @package AnyB1s\Data\Common\EventSourcing
 */
interface EventSourcedAggregate
{
    /**
     * @return string
     */
    public function id(): string;

    /**
     * @return object[]
     */
    public function popRecordedEvents();

    /**
     * @param array $events
     * @return static
     */
    public static function reconstitute(array $events);
}