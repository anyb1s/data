<?php

namespace AnyB1s\Data\Common\EventSourcing\EventStore;

use JMS\Serializer\SerializerInterface;
use Ramsey\Uuid\Uuid;

final class EventStore
{
    private $storageFacility;
    private $eventDispatcher;
    private $serializer;

    /**
     * EventStore constructor.
     * @param $storageFacility
     * @param $eventDispatcher
     * @param SerializerInterface $serializer
     */
    public function __construct($storageFacility, $eventDispatcher, SerializerInterface $serializer)
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
     * @param EventEnvelope $eventEnvelope
     * @return object Of type $eventEnvelope->eventType()
     */
    private function restoreEvent(EventEnvelope $eventEnvelope)
    {
        return $this->serializer->deserialize($eventEnvelope->payload(), $eventEnvelope->eventType(), 'json');
    }
}