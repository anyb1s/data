<?php

namespace AnyB1s\Data\Domain\Model;

interface RecordEvents
{
    /**
     * @return array
     */
    public function recordedEvents() : array;
}