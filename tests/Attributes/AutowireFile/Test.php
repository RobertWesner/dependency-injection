<?php

declare(strict_types=1);

namespace RobertWesner\DependencyInjection\Tests\Attributes\AutowireFile;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use RobertWesner\DependencyInjection\Attributes\AutowireFile;
use RobertWesner\DependencyInjection\Buffer;
use RobertWesner\DependencyInjection\Container;
use RobertWesner\DependencyInjection\Exception\AutowireException;
use RobertWesner\DependencyInjection\Tests\Attributes\AutowireFile\Fixtures\Bar;
use RobertWesner\DependencyInjection\Tests\Attributes\AutowireFile\Fixtures\Buffered;
use RobertWesner\DependencyInjection\Tests\Attributes\AutowireFile\Fixtures\Foo;

#[CoversClass(AutowireFile::class)]
#[UsesClass(Container::class)]
#[UsesClass(Buffer::class)]
class Test extends TestCase
{
    /**
     * @throws ContainerExceptionInterface
     * @throws AutowireException
     * @throws NotFoundExceptionInterface
     */
    public function testValid(): void
    {
        $container = new Container();

        $foo = $container->get(Foo::class);
        self::assertInstanceOf(Foo::class, $foo);
        self::assertSame("Hello World!\n", (string)$foo);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws AutowireException
     * @throws NotFoundExceptionInterface
     */
    public function testMissingFile(): void
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
    public function testBuffered(): void
    {
        $container = new Container();

        $buffered = $container->get(Buffered::class);
        self::assertSame("Hello World!\nHello World!\n", (string)$buffered);
    }
}
