<?php

namespace AnyB1s\Data\Common\EventSourcing\Aggregate\Repository;

use AnyB1s\Data\Common\EventSourcing\EventSourcedAggregate;
use AnyB1s\Data\Common\EventSourcing\EventStore\EventStore;
use Assert\Assert;

final class EventSourcedAggregateRepository
{
    /** @var EventStore */
    private $eventStore;
    /** @var string */
    private $aggregateType;

    public function __construct(EventStore $eventStore, string $aggregateType)
    {
        $this->eventStore = $eventStore;
        $this->aggregateType = $aggregateType;
    }

    public function save(EventSourcedAggregate $aggregate)
    {
        Assert::that($aggregate)
            ->same(get_class($aggregate), $this->aggregateType);

        $this->eventStore->append($this->aggregateType, $aggregate->id(), $aggregate->popRecordedEvents());
    }

    /**
     * @param string $id
     * @return object Of type $this->aggregateType
     */
    public function getById(string $id)
    {
        return $this->eventStore->reconstitute($this->aggregateType, $id);
    }
}