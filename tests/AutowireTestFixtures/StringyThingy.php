<?php

declare(strict_types=1);

namespace RobertWesner\DependencyInjection\Tests\AutowireTestFixtures;

use Stringable;

class StringyThingy implements Stringable
{
    public function __toString(): string
    {
        return 'Thingy';
    }
}
