<?php

namespace RobertWesner\DependencyInjection\Tests;

use PHPUnit\Framework\Attributes\BackupGlobals;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use RobertWesner\DependencyInjection\Container;
use RobertWesner\DependencyInjection\Tests\Classes\Foo;

/**
 * This is a big blackbox test and should be replaced, or rather expanded, with properly mocked unit tests.
 */
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
