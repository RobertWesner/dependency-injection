<?php

declare(strict_types=1);

namespace RobertWesner\DependencyInjection\Tests\Attributes\AutowireCallable;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use RobertWesner\DependencyInjection\Attributes\AutowireCallable;
use RobertWesner\DependencyInjection\Container;
use RobertWesner\DependencyInjection\Exception\AutowireException;
use RobertWesner\DependencyInjection\Tests\Attributes\AutowireCallable\Fixtures\Bar;
use RobertWesner\DependencyInjection\Tests\Attributes\AutowireCallable\Fixtures\Foo;

#[CoversClass(AutowireCallable::class)]
#[UsesClass(Container::class)]
class Test extends TestCase
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testInvalid(): void
    {
        $container = new Container();

        $this->expectException(AutowireException::class);
        $container->get(Bar::class);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws AutowireException
     * @throws NotFoundExceptionInterface
     */
    public function test(): void
    {
        $container = new Container();

        $foo = $container->get(Foo::class);
        self::assertSame(['b' => 'a'], $foo->test());
    }
}
