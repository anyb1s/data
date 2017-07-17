<?php

namespace AnyB1s\Data\Common\EventSourcing\EventStore;

final class EventStore
{
    private $storageFacility;
    private $eventDispatcher;
    private $serializer;

    /**
     * EventStore constructor.
     * @param $storageFacility
     * @param $eventDispatcher
     * @param $serializer
     */
    public function __construct($storageFacility, $eventDispatcher, $serializer)
    {
        $this->storageFacility = $storageFacility;
        $this->eventDispatcher = $eventDispatcher;
        $this->serializer = $serializer;
    }

    public function append(string $aggregateType, string $aggregateId, array $events)
    {
        foreach ($events as $event) {
            $envelope = $this->wrapInEnvelope($aggregateType, $aggregateId, $event);
            $this->storageFacility->append($envelope);
            $this->eventDispatcher->dispatch($event);
        }
    }

    public function loadEventsOf(string $aggregateType, string $aggregateId)
    {
        return [];
    }

    public function reconstitute(string $aggregateType, string $aggregateId)
    {
        $events = $this->loadEventsOf($aggregateType, $aggregateId);

        if (! $events) {
            throw AggregateNotFound::withClassAndId($aggregateType, $aggregateId);
        }

        return call_user_func([$aggregateType, 'reconstitute'], $events);
    }

    private function wrapInEnvelope(string $aggregateType, string $aggregateId, $event): EventEnvelope
    {
        $id = mt_rand(1, 5);
        $eventType = get_class($event);
        $payload = [];
        $now = new \DateTimeImmutable();

        return new EventEnvelope(
            $id,
            $aggregateType,
            $aggregateId,
            $eventType,
            $now,
            $payload
        );
    }
}