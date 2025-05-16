<?php

declare(strict_types=1);

namespace RobertWesner\DependencyInjection\Tests\AutowireTestFixtures;

use RobertWesner\DependencyInjection\Attributes\AutowireCallable;
use RobertWesner\DependencyInjection\Attributes\AutowireEnv;
use RobertWesner\DependencyInjection\Attributes\AutowireGlobal;
use RobertWesner\DependencyInjection\Attributes\AutowireJson;
use RobertWesner\DependencyInjection\Attributes\AutowireValue;

readonly class Foo
{
    public function __construct(
        private Bar $bar,
        #[AutowireValue(Moody::MOOD)]
        private string $fromConst,
        #[AutowireGlobal('GLOBALS', 'demo')]
        private string $fromGlobal,
        #[AutowireEnv(__DIR__ . '/foo.env', 'TEST')]
        private string $fromEnv,
        #[AutowireJson(__DIR__ . '/foo.json', '$.test.value')]
        private int $fromJson,
        #[AutowireCallable([StaticProvider::class, 'provide'], ['123', 'test'])]
        private string $fromCallable,
    ) {}

    public function test(): string
    {
        $bar = (string)$this->bar;
        $const = $this->fromConst;
        $global = $this->fromGlobal;
        $env = $this->fromEnv;
        $json = $this->fromJson;
        $callable = $this->fromCallable;

        return <<<EOF
            Bar:        $bar
            Value:      $const
            Global:     $global
            Env:        $env
            JSON:       $json
            Callable:   $callable
            EOF;
    }
}
