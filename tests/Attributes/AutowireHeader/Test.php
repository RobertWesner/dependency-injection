<?php

declare(strict_types=1);

namespace RobertWesner\DependencyInjection\Tests\Attributes\AutowireHeader;

use PHPUnit\Framework\Attributes\BackupGlobals;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use RobertWesner\DependencyInjection\Attributes\AutowireHeader;
use RobertWesner\DependencyInjection\Container;
use RobertWesner\DependencyInjection\Exception\AutowireException;
use RobertWesner\DependencyInjection\Tests\Attributes\AutowireHeader\Fixtures\Foo;

#[CoversClass(AutowireHeader::class)]
#[UsesClass(Container::class)]
class Test extends TestCase
{
    /**
     * @throws ContainerExceptionInterface
     * @throws AutowireException
     * @throws NotFoundExceptionInterface
     */
    public function testMissing(): void
    {
        $container = new Container();

        $foo = $container->get(Foo::class);
        self::assertNull($foo->getAuthentication());
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws AutowireException
     * @throws NotFoundExceptionInterface
     */
    #[BackupGlobals(true)]
    public function test(): void
    {
        $container = new Container();

        $_SERVER['HTTP_X_MY_CUSTOM_AUTH'] = '1234567890';
        $foo = $container->get(Foo::class);
        self::assertSame('1234567890', $foo->getAuthentication());
    }
}
