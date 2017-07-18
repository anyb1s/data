<?php

namespace AnyB1s\Data\Common\EventSourcing;

use AnyB1s\Data\Common\Persistence\Entity;

/**
 * Interface EventSourcedAggregate
 * @package AnyB1s\Data\Common\EventSourcing
 */
interface EventSourcedAggregate extends Entity
{
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