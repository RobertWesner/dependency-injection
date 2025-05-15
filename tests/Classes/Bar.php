<?php

namespace RobertWesner\DependencyInjection\Tests\Classes;

use Stringable;

readonly class Bar implements Stringable
{
    public function __construct(
        private StringyThingy $thing,
    ) {}

    public function __toString(): string
    {
        return (string)$this->thing;
    }
}
