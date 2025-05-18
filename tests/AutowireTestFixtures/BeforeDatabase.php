<?php

declare(strict_types=1);

namespace RobertWesner\DependencyInjection\Tests\AutowireTestFixtures;

use RobertWesner\DependencyInjection\Attributes\AutowireEnv;
use RobertWesner\DependencyInjection\Attributes\BufferFile;

readonly class BeforeDatabase
{
    public function __construct(
        #[AutowireEnv(__DIR__ . '/.env', 'SOMETHING_RANDOM')]
        #[BufferFile]
        private string $abc,
    ) {}

    public function test(): string
    {
        return $this->abc;
    }
}
