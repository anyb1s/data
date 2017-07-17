<?php

namespace AnyB1s\Data\Common\EventSourcing\EventStore\Storage;

use AnyB1s\Data\Common\EventSourcing\EventStore\EventEnvelope;
use AnyB1s\Data\Common\EventSourcing\EventStore\StorageFacility;
use Assert\Assert;
use Aura\Sql\ExtendedPdoInterface;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;

final class DatabaseStorageFacility implements StorageFacility
{
    /** @var ExtendedPdoInterface $connection */
    private $connection;

    /**
     * DatabaseStorageFacility constructor.
     * @param ExtendedPdoInterface $connection
     */
    public function __construct(ExtendedPdoInterface $connection)
    {
        $this->connection = $connection;
    }

    public function loadEventsOf(string $aggregateType, string $aggregateId): array
    {
        $table = $this->tableName($aggregateType);
        
        $statement = $this->connection->prepare(
            "SELECT `payload` FROM `{$table}` WHERE aggregate_type= ? AND aggregate_id = ?"
        );
        $statement->bindValue(1, $aggregateType);
        $statement->bindValue(2, $aggregateId);

        return $statement->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function loadAllEvents(): array
    {
        return Database::retrieveAll(EventEnvelope::class);
    }

    public function append(EventEnvelope $eventEnvelope): void
    {
        $table = $this->tableName($eventEnvelope->aggregateType());
        
        $statement = $this->connection->prepare("INSERT INTO `{$table}` VALUES (?, ?)");

        $statement->bindValue(1, $eventEnvelope->aggregateType());
        $statement->bindValue(2, $eventEnvelope->aggregateId());
        $statement->execute();
    }

    public function deleteAll(): void
    {
        Database::deleteAll(EventEnvelope::class);
    }

    private function tableName(string $aggregateType) : string
    {
        return (new CamelCaseToSnakeCaseNameConverter())->normalize($aggregateType);
    }
}
