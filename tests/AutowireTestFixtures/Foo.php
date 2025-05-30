<?php

declare(strict_types=1);

namespace RobertWesner\DependencyInjection\Tests\AutowireTestFixtures;

use RobertWesner\DependencyInjection\Attributes\AutowireCallable;
use RobertWesner\DependencyInjection\Attributes\AutowireEnv;
use RobertWesner\DependencyInjection\Attributes\AutowireFile;
use RobertWesner\DependencyInjection\Attributes\AutowireGlobal;
use RobertWesner\DependencyInjection\Attributes\AutowireHeader;
use RobertWesner\DependencyInjection\Attributes\AutowireJson;
use RobertWesner\DependencyInjection\Attributes\AutowireToml;
use RobertWesner\DependencyInjection\Attributes\AutowireValue;
use RobertWesner\DependencyInjection\Attributes\AutowireXml;
use RobertWesner\DependencyInjection\Attributes\AutowireYaml;

readonly class Foo
{
    public function __construct(
        private Bar $bar,
        #[SomeRandomAttribute]
        #[AutowireValue(Moody::MOOD)]
        private string $fromConst,
        #[AutowireGlobal('GLOBALS', 'demo')]
        private string $fromGlobal,
        #[AutowireEnv(__DIR__ . '/foo.ini', 'TEST')]
        private string $fromEnv,
        #[AutowireJson(__DIR__ . '/foo.json', '$.test.value')]
        private int $fromJson,
        #[AutowireCallable([StaticProvider::class, 'provide'], ['123', 'test'])]
        private string $fromCallable,
        #[AutowireFile(__DIR__ . '/cat.txt')]
        private string $asciiCat,
        #[AutowireXml(__DIR__ . '/test.xml', '/document/chapters/chapter[2]/@title')]
        private array $chapter2TitleResult,
        #[AutowireYaml(__DIR__ . '/foo.yaml', '$.test.value')]
        private int $fromYaml,
        #[AutowireToml(__DIR__ . '/foo.toml', '$.database')]
        private array $fromToml,
        #[AutowireHeader('X-Test-Whatever')]
        private ?string $whatever,
        private string $defaulted = 'yep',
    ) {}

    public function test(): string
    {
        $toml = var_export($this->fromToml, true);

        return <<<EOF
            Bar:        $this->bar
            Value:      $this->fromConst
            Global:     $this->fromGlobal
            Env:        $this->fromEnv
            JSON:       $this->fromJson
            Callable:   $this->fromCallable
            XML:        {$this->chapter2TitleResult[0]}
            YAML:       $this->fromYaml
            Header:     $this->whatever
            Default:    $this->defaulted
            
            TOML:
            $toml
            
            $this->asciiCat
            EOF;
    }
}
