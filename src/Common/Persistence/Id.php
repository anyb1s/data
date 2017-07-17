<?php

namespace AnyB1s\Data\Common\Persistence;

interface Id
{
    public function __toString() : string;
}