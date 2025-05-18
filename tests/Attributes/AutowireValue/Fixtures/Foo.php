<?php

declare(strict_types=1);

namespace RobertWesner\DependencyInjection\Tests\Attributes\AutowireValue\Fixtures;

use RobertWesner\DependencyInjection\Attributes\AutowireValue;

final readonly class Foo
{
    public function __construct(
        #[AutowireValue(123)]
        public int $a,
        #[AutowireValue(10.2)]
        public float $b,
        #[AutowireValue('test')]
        public string $c,
        #[AutowireValue(['leet' => 1337])]
        public array $d,
    ) {}
}
