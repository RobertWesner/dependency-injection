<h1 align="center">
PSR-11 container with autowiring
</h1>

<div align="center">

![](https://github.com/RobertWesner/dependency-injection/actions/workflows/tests.yml/badge.svg)
![](https://raw.githubusercontent.com/RobertWesner/dependency-injection/image-data/coverage.svg)
![](https://img.shields.io/github/v/release/RobertWesner/dependency-injection)
[![License: MIT](https://img.shields.io/github/license/RobertWesner/dependency-injection)](../../raw/main/LICENSE.txt)

</div>

## What is this?

This is a small and fun PSR-11 container implementation with autowiring.

It provides plenty of alternative ways to autowire non-object values
from .env, JSON files, constants, and more via PHP Attributes.


## Installation

```bash
composer require robertwesner/dependency-injection
```


## Usage

```php
// Instantiate new container
$container = new Container();

// Load class MyClass with all it's dependencies. Store and return its instance.
$instance = $container->get(MyClass::class);
$instance->myMethod('Some text.', 1234);
```


## Autowiring non-instance values

This package provides multiple ways to load scalar values and arrays into classes.

```php
readonly class Foo
{
    public function __construct(
        // Resolves all dependencies for Bar, if any, and uses its instance
        private Bar $bar,
        
        // Load any fixed value, can take values from constants...
        #[AutowireValue(Moody::MOOD)]
        private string $myMood,
        // ...or value literals
        #[AutowireValue(1234)]
        private int $scalar,
        
        // Load from superglobals like _COOKIE, _SESSION, or GLOBALS
        #[AutowireGlobal('GLOBALS', 'demo')]
        private string $demo,
        
        // Call any function or static method, optionally with arguments
        #[AutowireCallable([StaticProvider::class, 'provide'], ['123', 'test'])]
        private string $provided,
        
        // Load a full file
        #[AutowireFile(__DIR__ . '/../../cat.txt')]
        private string $asciiCat,
        
        // Load from a .env, .env.local, or similar file
        #[AutowireEnv(__DIR__ . '/../../.env', 'TEST')]
        private string $envTest,
        
        // Load from JSON file based on JSONPath
        // Can return an array of all JSONPath matches with the "multiple" Parameter
        #[AutowireJson(__DIR__ . '/../../foo.json', '$.test.value')]
        private int $val,
        
        // Load from YAML file based on JSONPath
        // Can return an array of all JSONPath matches with the "multiple" Parameter
        #[AutowireYaml(__DIR__ . '/../../foo.yaml', '$.test.value')]
        private int $fromYaml,
        
        // Load from TOML file based on JSONPath
        // Can return an array of all JSONPath matches with the "multiple" Parameter
        #[AutowireToml(__DIR__ . '/../../config.toml', '$.database')]
        private array $databaseConfig,
        
        // Load an XML file and parses it as SimpleXml. Then applies xPath to it to acquire an array element result
        #[AutowireXml(__DIR__ . '/../../test.xml', '/document/chapters/chapter[2]/@title')]
        private array $chapter2TitleResult,
        
        // Reads a header and stores its value. Missing Headers are always NULL so all usages should be nullable!
        #[AutowireHeader('User-Agent')]
        private ?string $userAgent,
    ) {}
}
```


## Buffering autowired files for multiple access

When using a single file multiple times you should consider adding the Attribute `#[BufferFile]`
to store the (parsed) file in memory and load it when reused.

applicable to:
- `#[AutowireFile]`
- `#[AutowireJson]`
- `#[AutowireEnv]`
- `#[AutowireYaml]`
- `#[AutowireToml]`
- `#[AutowireXml]`

```php
readonly class DatabaseService
{
    public function __construct(
        #[AutowireEnv(__DIR__ . '../../.env', 'MYSQL_SERVER')]
        #[BufferFile]
        private string $dbServer,
        #[AutowireEnv(__DIR__ . '../../.env', 'MYSQL_USERNAME')]
        #[BufferFile]
        private string $dbUsername,
        #[AutowireEnv(__DIR__ . '../../.env', 'MYSQL_PASSWORD')]
        #[BufferFile]
        private string $dbPassword,
    ) {}
}
```
