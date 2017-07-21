<?php

namespace AnyB1s\Data\Common\EventSourcing\EventStore;

use AnyB1s\Data\Common\EventDispatcher\EventDispatcher;
use JMS\Serializer\SerializerInterface;
use Ramsey\Uuid\Uuid;

final class EventStore
{
    private $storageFacility;
    private $eventDispatcher;
    private $serializer;

    /**
     * EventStore constructor.
     * @param StorageFacility $storageFacility
     * @param EventDispatcher $eventDispatcher
     * @param SerializerInterface $serializer
     */
    public function __construct(StorageFacility $storageFacility, EventDispatcher $eventDispatcher, SerializerInterface $serializer)
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
        $events = [];
        foreach ($this->storageFacility->loadEventsOf($aggregateType, $aggregateId) as list($eventType, $rawEvent)) {
            $events[] = $this->restoreEvent($eventType, $rawEvent);
        }
        return $events;
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
        $id = (string) Uuid::uuid4();
        $eventType = get_class($event);
        $payload = $this->extractPayload($event);
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

    private function extractPayload($event): string
    {
        return $this->serializer->serialize($event, 'json');
    }

    /**
     * @param string $eventType
     * @param string $payload
     * @return object
     */
    private function restoreEvent(string $eventType, string $payload)
    {
        return $this->serializer->deserialize($payload, $eventType, 'json');
    }
}