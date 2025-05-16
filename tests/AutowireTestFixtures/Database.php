<?php

declare(strict_types=1);

namespace RobertWesner\DependencyInjection\Tests\AutowireTestFixtures;

use RobertWesner\DependencyInjection\Attributes\AutowireEnv;
use RobertWesner\DependencyInjection\Attributes\BufferFile;

readonly class Database
{
    public function __construct(
        #[AutowireEnv(__DIR__ . '/.env', 'MYSQL_SERVER')]
        #[BufferFile]
        private string $dbServer,
        #[AutowireEnv(__DIR__ . '/.env', 'MYSQL_USERNAME')]
        #[BufferFile]
        private string $dbUsername,
        #[AutowireEnv(__DIR__ . '/.env', 'MYSQL_PASSWORD')]
        #[BufferFile]
        private string $dbPassword,
    ) {}

    public function test(): string
    {
        return 'connect(' . $this->dbServer . ', ' . $this->dbUsername . ', ' . $this->dbPassword . ')';
    }
}
