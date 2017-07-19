<?php

namespace AnyB1s\Data\Common\EventSourcing;

use Assert\Assertion;

/**
 * Class EventSourcedAggregateCapabilities
 * @package AnyB1s\Data\Common\EventSourcing
 */
trait EventSourcedAggregateCapabilities
{
    /**
     * @var string
     */
    private $id;
    /**
     * @var object[]
     */
    private $recordedEvents = [];

    /**
     * Enforce construction through either a named constructor or the `reconstitute()` method.
     */
    private function __construct()
    {
    }

    /**
     * @return \object[]
     */
    public function popRecordedEvents()
    {
        $recordedEvents = $this->recordedEvents;
        $this->recordedEvents = [];

        return $recordedEvents;
    }

    /**
     * @return string
     */
    public function id(): string
    {
        $id = is_string($this->id) ? $this->id : (string)$this->id;

        Assertion::notEmpty($id, 'Aggregate ID is empty');

        return $id;
    }

    /**
     * @param array $events
     * @return static
     */
    public static function reconstitute(array $events)
    {
        $instance = new static();
        foreach ($events as $event) {
            $instance->apply($event);
        }

        return $instance;
    }

    /**
     * @param $event
     */
    private function recordThat($event)
    {
        Assertion::isObject($event, 'A domain event should be an object');

        $this->recordedEvents[] = $event;
        $this->apply($event);
    }

    /**
     * @param $event
     */
    private function apply($event)
    {
        Assertion::isObject($event, 'A domain event should be an object');

        $parts = explode('\\', get_class($event));
        $eventName = end($parts);
        $name = 'when'.$eventName;
        $applyFunction = [$this, $name];

        Assertion::true(is_callable($applyFunction), sprintf(
            'You first need to define the following method in class %s: private function %s(%s $event) { }',
            get_class($this),
            $name,
            get_class($event)
        ));

        call_user_func($applyFunction, $event);
    }
}
