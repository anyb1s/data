<?php

namespace AnyB1s\Data\Common\EventSourcing\EventStore\Event;

use AnyB1s\Data\Common\EventSourcing\EventStore\EventEnvelope;
use Symfony\Component\EventDispatcher\Event;

class EventAppended extends Event
{
    /** @var EventEnvelope */
    private $envelope;

    /**
     * DomainEventAppended constructor.
     * @param EventEnvelope $envelope
     */
    public function __construct(EventEnvelope $envelope)
    {
        $this->envelope = $envelope;
    }

    /**
     * @return EventEnvelope
     */
    public function getEnvelope(): EventEnvelope
    {
        return $this->envelope;
    }
}