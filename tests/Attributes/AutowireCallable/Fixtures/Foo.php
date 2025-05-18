<?php

declare(strict_types=1);

namespace RobertWesner\DependencyInjection\Tests\Attributes\AutowireCallable\Fixtures;

use RobertWesner\DependencyInjection\Attributes\AutowireCallable;

final readonly class Foo
{
    public function __construct(
        #[AutowireCallable('array_flip', [['a' => 'b']])]
        private array $test,
    ) {}

    public function test(): array
    {
        return $this->test;
    }
}
