<?php

declare(strict_types=1);

namespace RobertWesner\DependencyInjection\Tests\AutowireTestFixtures\Class;

readonly class Circular
{
    public function __construct(
        public Smirkular $smirkular,
    ) {}
}
