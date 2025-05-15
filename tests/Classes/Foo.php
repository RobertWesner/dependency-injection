<?php

namespace RobertWesner\DependencyInjection\Tests\Classes;

use RobertWesner\DependencyInjection\Attributes\AutowireEnv;
use RobertWesner\DependencyInjection\Attributes\AutowireGlobal;
use RobertWesner\DependencyInjection\Attributes\AutowireJson;

readonly class Foo
{
    public function __construct(
        private Bar $bar,
        #[AutowireGlobal('GLOBALS', 'demo')]
        private string $fromGlobal,
        #[AutowireEnv(__DIR__ . '/foo.env', 'TEST')]
        private string $fromEnv,
        #[AutowireJson(__DIR__ . '/foo.json', '$.test.value')]
        private int $fromJson,
    ) {}

    public function test(): string
    {
        $bar = (string)$this->bar;
        $global = $this->fromGlobal;
        $env = $this->fromEnv;
        $json = $this->fromJson;

        return <<<EOF
            Bar:    $bar
            Global: $global
            Env:    $env
            JSON:   $json
            EOF;
    }
}
