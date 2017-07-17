<?php

namespace AnyB1s\Data\Common\EventSourcing\EventStore;

final class AggregateNotFound extends \RuntimeException
{
    public static function withClassAndId(string $aggregateClass, string $id): AggregateNotFound
    {
        return new self(sprintf(
            'Could not find aggregate of type "%s" with ID "%s"',
            $aggregateClass,
            $id
        ));
    }
}