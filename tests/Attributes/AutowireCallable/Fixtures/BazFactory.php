<?php

declare(strict_types=1);

namespace RobertWesner\DependencyInjection\Tests\Attributes\AutowireCallable\Fixtures;

class BazFactory
{
    public static function create(): Baz
    {
        return new Baz(':)');
    }
}
