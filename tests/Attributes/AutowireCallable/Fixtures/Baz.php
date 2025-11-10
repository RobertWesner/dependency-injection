<?php

declare(strict_types=1);

namespace RobertWesner\DependencyInjection\Tests\Attributes\AutowireCallable\Fixtures;

class Baz {
    public function __construct(
        public string $test,
    ) {}
}
