<?php

declare(strict_types=1);

namespace RobertWesner\DependencyInjection\Tests\AutowireTestFixtures;

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
