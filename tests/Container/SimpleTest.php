<?php

declare(strict_types=1);

namespace RobertWesner\DependencyInjection\Tests\Container;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use RobertWesner\DependencyInjection\Container;
use RobertWesner\DependencyInjection\Exception\AutowireException;
use RobertWesner\DependencyInjection\Exception\ContainerException;
use RobertWesner\DependencyInjection\Exception\NotFoundException;
use RobertWesner\DependencyInjection\Tests\AutowireTestFixtures\Impossible;
use RobertWesner\DependencyInjection\Tests\AutowireTestFixtures\Typeless;

#[CoversClass(Container::class)]
final class SimpleTest extends TestCase
{
    /**
     * @throws ContainerExceptionInterface
     * @throws AutowireException
     * @throws NotFoundExceptionInterface
     */
    public function test(): void
    {
        $container = new Container();

        self::assertFalse($container->has('foo'));
        $foo = new class {};
        $container->set('foo', $foo);
        self::assertTrue($container->has('foo'));
        self::assertSame($foo, $container->get('foo'));
    }

    /**
     * @throws AutowireException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testMissing(): void
    {
        $container = new Container();

        self::assertFalse($container->has('something'));
        self::expectException(NotFoundException::class);
        $container->get('something');
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws AutowireException
     * @throws NotFoundExceptionInterface
     */
    public function testTypeless(): void
    {

        $container = new Container();

        self::expectException(ContainerException::class);
        $container->get(Typeless::class);
    }

    /**
     * @throws AutowireException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testImpossible(): void
    {

        $container = new Container();

        self::expectException(ContainerException::class);
        $container->get(Impossible::class);
    }
}
