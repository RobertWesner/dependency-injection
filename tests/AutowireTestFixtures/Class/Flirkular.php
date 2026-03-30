<?php

declare(strict_types=1);

namespace RobertWesner\DependencyInjection\Tests\AutowireTestFixtures\Class;

readonly class Flirkular
{
    public function __construct(
        public Circular $cirkular,
    ) {}
}
