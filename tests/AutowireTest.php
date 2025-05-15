<?php

declare(strict_types=1);

namespace RobertWesner\DependencyInjection\Tests;

use PHPUnit\Framework\Attributes\BackupGlobals;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use RobertWesner\DependencyInjection\Attributes\AutowireEnv;
use RobertWesner\DependencyInjection\Attributes\AutowireGlobal;
use RobertWesner\DependencyInjection\Attributes\AutowireJson;
use RobertWesner\DependencyInjection\Container;
use RobertWesner\DependencyInjection\Tests\Classes\Foo;

/**
 * This is a big blackbox test and should be replaced, or rather expanded, with properly mocked unit tests.
 */
#[CoversClass(Container::class)]
#[CoversClass(AutowireGlobal::class)]
#[CoversClass(AutowireEnv::class)]
#[CoversClass(AutowireJson::class)]
final class AutowireTest extends TestCase
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[BackupGlobals(true)]
    public function test(): void
    {
        $container = new Container();

        $GLOBALS['demo'] = ':)';
        $foo = $container->get(Foo::class);
        self::assertInstanceOf(Foo::class, $foo);
        self::assertSame(
            <<<EOF
                Bar:    Thingy
                Global: :)
                Env:    Funny Value Here!
                JSON:   1337
                EOF,
            $foo->test(),
        );
    }
}
