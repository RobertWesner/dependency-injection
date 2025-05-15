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

## Autowiring non-object values

This package provides multiple ways to load scalar values and arrays into classes.

```php
readonly class Foo
{
    public function __construct(
        // Resolves all dependencies for Bar, if any, and uses its instance
        private Bar $bar,
        
        // Load from superglobals like _COOKIE, _SESSION, or GLOBALS
        #[AutowireGlobal('GLOBALS', 'demo')]
        private string $fromGlobal,
        
        // Load from a .env, .env.local, or similar file
        #[AutowireEnv(__DIR__ . '/foo.env', 'TEST')]
        private string $fromEnv,
        
        // Load from JSON file based on path
        // $.test.value accesses the following value:
        //  {
        //      "test": {
        //          "value": 1337
        //      }
        //  } 
        #[AutowireJson(__DIR__ . '/foo.json', '$.test.value')]
        private int $fromJson,
    ) {}
}
```
