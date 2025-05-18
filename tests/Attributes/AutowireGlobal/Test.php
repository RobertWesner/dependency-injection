<?php

declare(strict_types=1);

namespace RobertWesner\DependencyInjection\Tests\Attributes\AutowireGlobal;

use PHPUnit\Framework\Attributes\BackupGlobals;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use RobertWesner\DependencyInjection\Attributes\AutowireGlobal;
use RobertWesner\DependencyInjection\Container;
use RobertWesner\DependencyInjection\Exception\AutowireException;
use RobertWesner\DependencyInjection\Tests\Attributes\AutowireGlobal\Fixtures\Foo;

#[CoversClass(AutowireGlobal::class)]
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

        $this->expectException(AutowireException::class);
        $container->get(Foo::class);
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

        $GLOBALS['login'] = 'd81c14bb-0dd6-4fdc-90a9-aa637143e8f6';
        $foo = $container->get(Foo::class);
        self::assertSame('d81c14bb-0dd6-4fdc-90a9-aa637143e8f6', $foo->getLogin());
    }
}
