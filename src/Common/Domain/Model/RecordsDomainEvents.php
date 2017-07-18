<?php

namespace AnyB1s\Data\Domain\Model;

interface RecordsDomainEvents
{
    /**
     * @return array
     */
    public function recordedEvents() : array;
}