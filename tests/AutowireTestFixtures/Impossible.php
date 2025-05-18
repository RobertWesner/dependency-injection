<?php

declare(strict_types=1);

namespace RobertWesner\DependencyInjection\Tests\AutowireTestFixtures;

use Stringable;

class Impossible implements Stringable
{
    public function __construct(
        private readonly string $foo,
    ) {}

    public function __toString()
    {
        return var_export($this->foo, true);
    }
}
