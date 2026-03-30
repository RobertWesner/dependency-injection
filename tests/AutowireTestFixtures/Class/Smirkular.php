<?php

declare(strict_types=1);

namespace RobertWesner\DependencyInjection\Tests\AutowireTestFixtures\Class;

readonly class Smirkular
{
    public function __construct(
        public Flirkular $flirkular,
    ) {}
}
