<?php

namespace AnyB1s\Data\Common\EventSourcing\EventStore\Storage;

use AnyB1s\Data\Common\EventSourcing\EventStore\EventEnvelope;
use AnyB1s\Data\Common\EventSourcing\EventStore\StorageFacility;
use Assert\Assertion;
use Illuminate\Database\Connection;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;

class LaravelMysqlStorage implements StorageFacility
{
    /** @var Connection */
    private $connection;
    /** @var string */
    private $table;

    /**
     * LaravelDatabaseRepository constructor.
     * @param Connection $connection
     * @param string $table
     */
    public function __construct(Connection $connection, string $table)
    {
        $table = (new CamelCaseToSnakeCaseNameConverter())->normalize($table);

        Assertion::endsWith($table, '_event_store');

        $this->connection = $connection;
        $this->table = $table;
    }

    public function loadEventsOf(string $aggregateType, string $aggregateId): array
    {
        return $this->connection
            ->table($this->table)
            ->select(['payload'])
            ->where('aggregate_type', $aggregateType)
            ->where('aggregate_id', $aggregateId)
            ->get()
            ->pluck('payload')
            ->all();
    }

    public function loadAllEvents(): array
    {
        return $this->connection
            ->table($this->table)
            ->select(['payload'])
            ->get()
            ->pluck('payload')
            ->all();
    }

    public function append(EventEnvelope $eventEnvelope): void
    {
        $this->connection
            ->table($this->table)
            ->insert([
                'id'             => $eventEnvelope->id(),
                'event_type'     => $eventEnvelope->eventType(),
                'aggregate_type' => $eventEnvelope->aggregateType(),
                'aggregate_id'   => $eventEnvelope->aggregateId(),
                'occurred_at'    => $eventEnvelope->occurredAt()->format(EventEnvelope::DATE_TIME_FORMAT),
                'payload'        => $eventEnvelope->payload(),
            ]);
    }

    public function deleteAll(): void
    {
        $this->connection->table($this->table)->delete();
    }
}