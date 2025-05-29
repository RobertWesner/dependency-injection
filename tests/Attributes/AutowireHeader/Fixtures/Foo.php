<?php

declare(strict_types=1);

namespace RobertWesner\DependencyInjection\Tests\Attributes\AutowireHeader\Fixtures;

use RobertWesner\DependencyInjection\Attributes\AutowireHeader;

final readonly class Foo
{
    public function __construct(
        #[AutowireHeader('X-My-Custom-Auth')]
        private ?string $authentication,
    ) {}

    public function getAuthentication(): ?string
    {
        return $this->authentication;
    }
}
