<?php

namespace AnyB1s\Data\Common\EventSourcing\EventStore\Storage;

use AnyB1s\Data\Common\EventSourcing\EventStore\EventEnvelope;
use AnyB1s\Data\Common\EventSourcing\EventStore\StorageFacility;

final class DatabaseStorageFacility implements StorageFacility
{
    /** @var \Doctrine\DBAL\Connection  */
    private $connection;

    /**
     * DatabaseStorageFacility constructor.
     * @param \Doctrine\DBAL\Connection $connection
     */
    public function __construct(\Doctrine\DBAL\Connection $connection)
    {
        $this->connection = $connection;
    }

    public function loadEventsOf(string $aggregateType, string $aggregateId): array
    {
        $sql = $this->connection->createQueryBuilder()
            ->where('AggregateType', $aggregateType)
            ->where('AggregateId', $aggregateId)
            ->getSQL();

        $this->connection->query($sql);

        return array_filter(
            $this->loadAllEvents(),
            function (EventEnvelope $eventEnvelope) use ($aggregateId, $aggregateType) {
                return $eventEnvelope->aggregateType() === $aggregateType
                    && $eventEnvelope->aggregateId() === $aggregateId;
            }
        );
    }
    public function loadAllEvents(): array
    {
        return Database::retrieveAll(EventEnvelope::class);
    }
    public function append(EventEnvelope $eventEnvelope): void
    {
        Database::persist($eventEnvelope);
    }
    public function deleteAll(): void
    {
        Database::deleteAll(EventEnvelope::class);
    }
}
