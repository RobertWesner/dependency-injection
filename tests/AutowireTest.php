<?php

declare(strict_types=1);

namespace RobertWesner\DependencyInjection\Tests;

use PHPUnit\Framework\Attributes\BackupGlobals;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
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
use RobertWesner\DependencyInjection\Buffer;
use RobertWesner\DependencyInjection\Container;
use RobertWesner\DependencyInjection\Tests\AutowireTestFixtures\BeforeDatabase;
use RobertWesner\DependencyInjection\Tests\AutowireTestFixtures\Database;
use RobertWesner\DependencyInjection\Tests\AutowireTestFixtures\Foo;

/**
 * This is a big blackbox test and should be replaced, or rather expanded, with properly mocked unit tests.
 */
#[CoversClass(Container::class)]
#[CoversClass(AutowireGlobal::class)]
#[CoversClass(AutowireEnv::class)]
#[CoversClass(AutowireJson::class)]
#[CoversClass(AutowireValue::class)]
#[CoversClass(AutowireGlobal::class)]
#[CoversClass(AutowireEnv::class)]
#[CoversClass(AutowireJson::class)]
#[CoversClass(AutowireCallable::class)]
#[CoversClass(AutowireFile::class)]
#[CoversClass(AutowireXml::class)]
#[CoversClass(AutowireYaml::class)]
#[CoversClass(AutowireToml::class)]
#[CoversClass(AutowireHeader::class)]
#[CoversClass(Buffer::class)]
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
        $_SERVER['HTTP_X_TEST_WHATEVER'] = '1339';
        $foo = $container->get(Foo::class);
        self::assertInstanceOf(Foo::class, $foo);
        self::assertSame(
            <<<EOF
                Bar:        Thingy
                Value:      feelin' alright
                Global:     :)
                Env:        Funny Value Here!
                JSON:       1337
                Callable:   static(123, test)
                XML:        Sed imperdiet
                YAML:       1338
                Header:     1339
                Default:    yep

                TOML:
                array (
                  'enabled' => true,
                  'ports' => 
                  array (
                    0 => 8000,
                    1 => 8001,
                    2 => 8002,
                  ),
                  'data' => 
                  array (
                    0 => 
                    array (
                      0 => 'delta',
                      1 => 'phi',
                    ),
                    1 => 
                    array (
                      0 => 3.14,
                    ),
                  ),
                  'temp_targets' => 
                  array (
                    'cpu' => 79.5,
                    'case' => 72.0,
                  ),
                )

                 /\___/\
                | ' . ' |
                 \_____/
                
                EOF,
            $foo->test(),
        );
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[BackupGlobals(true)]
    public function testReuseEnv(): void
    {
        $container = new Container();

        self::assertNotFalse(
            file_put_contents(
                __DIR__ . '/AutowireTestFixtures/.env',
                <<<ENV
                    SOMETHING_RANDOM="abc"
                    MYSQL_SERVER="localhost"
                    MYSQL_USERNAME="root"
                    MYSQL_PASSWORD="verysecure"
                    ENV,
            ),
        );

        $container->get(BeforeDatabase::class);

        // since it is buffered, it will still be available after deletion
        // this is the way to test single access
        unlink(__DIR__ . '/AutowireTestFixtures/.env');

        $database = $container->get(Database::class);
        self::assertInstanceOf(Database::class, $database);
        self::assertSame(
            'connect(localhost, root, verysecure)',
            $database->test(),
        );
    }
}
