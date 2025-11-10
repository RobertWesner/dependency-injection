<?php

declare(strict_types=1);

namespace RobertWesner\DependencyInjection\Tests\Attributes\AutowireCallable\Fixtures;

use RobertWesner\DependencyInjection\Attributes\AutowireCallable;

final readonly class Faz
{
    public function __construct(
        #[AutowireCallable([BazFactory::class, 'create'])]
        public Baz $baz,
    ) {}
}
