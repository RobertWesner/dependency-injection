<?php

declare(strict_types=1);

namespace RobertWesner\DependencyInjection\Tests\Attributes\AutowireGlobal\Fixtures;

use RobertWesner\DependencyInjection\Attributes\AutowireGlobal;

final readonly class Foo
{
    public function __construct(
        #[AutowireGlobal('_SESSION', 'login')]
        private string $login,
    ) {}

    public function getLogin(): string
    {
        return $this->login;
    }
}
