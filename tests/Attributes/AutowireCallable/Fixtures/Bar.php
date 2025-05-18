<?php

declare(strict_types=1);

namespace RobertWesner\DependencyInjection\Tests\Attributes\AutowireCallable\Fixtures;

use RobertWesner\DependencyInjection\Attributes\AutowireCallable;

final readonly class Bar
{
    public function __construct(
        #[AutowireCallable('AAAaaa???', [['a' => 'b']])]
        private array $test,
    ) {}

    public function test(): array
    {
        return $this->test;
    }
}
