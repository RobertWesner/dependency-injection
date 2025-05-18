<?php

declare(strict_types=1);

namespace RobertWesner\DependencyInjection\Tests\Attributes\AutowireValue;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use RobertWesner\DependencyInjection\Attributes\AutowireValue;
use RobertWesner\DependencyInjection\Container;
use RobertWesner\DependencyInjection\Exception\AutowireException;
use RobertWesner\DependencyInjection\Tests\Attributes\AutowireValue\Fixtures\Foo;

#[CoversClass(AutowireValue::class)]
#[UsesClass(Container::class)]
class Test extends TestCase
{
    /**
     * @throws ContainerExceptionInterface
     * @throws AutowireException
     * @throws NotFoundExceptionInterface
     */
    public function test(): void
    {
        $container = new Container();

        $foo = $container->get(Foo::class);
        self::assertSame(123, $foo->a);
        self::assertSame(10.2, $foo->b);
        self::assertSame('test', $foo->c);
        self::assertSame(['leet' => 1337], $foo->d);
    }
}
