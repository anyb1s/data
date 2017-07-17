<?php

namespace AnyB1s\Data\Domain\Model;

use Assert\Assert;

trait AggregateId
{
    /**
     * @var string
     */
    private $id;

    private function __construct()
    {
    }

    /**
     * @param string $id
     * @return static
     */
    public static function fromString(string $id)
    {
        Assert::that($id)
            ->notEmpty()
            ->uuid();

        $aggregateId = new static();
        $aggregateId->id = $id;

        return $aggregateId;
    }

    public function __toString(): string
    {
        return $this->id;
    }

    public function equals($otherAggregateId): bool
    {
        return get_class($otherAggregateId) === get_class($this)
            && (string)$this === (string)$otherAggregateId;
    }
}