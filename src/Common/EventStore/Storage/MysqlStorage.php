<?php

namespace AnyB1s\Data\Common\EventSourcing\EventStore\Storage;

use AnyB1s\Data\Common\EventSourcing\EventStore\EventEnvelope;
use AnyB1s\Data\Common\EventSourcing\EventStore\StorageFacility;
use Assert\Assert;
use PDO;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;

/**
 * Class MysqlStorageFacility
 * @package AnyB1s\Data\Common\EventSourcing\EventStore\Storage
 */
final class MysqlStorage implements StorageFacility
{
    /** @var PDO $connection */
    private $connection;
    /** @var string */
    private $table;

    /**
     * DatabaseStorageFacility constructor.
     * @param PDO $connection
     * @param string $table
     */
    public function __construct(PDO $connection, string $table)
    {
        $table = (new CamelCaseToSnakeCaseNameConverter())->normalize($table);

        Assert::that($table)
            ->endsWith('_event_store');

        $this->connection = $connection;
        $this->table = $table;
    }

    /**
     * @param string $aggregateType
     * @param string $aggregateId
     * @return array
     */
    public function loadEventsOf(string $aggregateType, string $aggregateId): array
    {
        $statement = $this->connection->prepare(
            "SELECT `payload` FROM `{$this->table}` WHERE aggregate_type= ? AND aggregate_id = ? ORDER BY occurred_at"
        );
        $statement->bindValue(1, $aggregateType);
        $statement->bindValue(2, $aggregateId);

        return $statement->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * @return array
     */
    public function loadAllEvents(): array
    {
        return $this->connection
            ->prepare("SELECT `payload` FROM `{$this->table}`")
            ->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * @param EventEnvelope $eventEnvelope
     */
    public function append(EventEnvelope $eventEnvelope): void
    {
        $statement = $this->connection->prepare("INSERT INTO `{$this->table}` VALUES (?, ?)");

        $statement->bindValue(1, $eventEnvelope->aggregateType());
        $statement->bindValue(2, $eventEnvelope->aggregateId());
        $statement->execute();
    }

    /**
     *
     */
    public function deleteAll(): void
    {
        $this->connection->query("DELETE FROM `{$this->table}`");
    }
}
