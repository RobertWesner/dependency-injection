<?php

declare(strict_types=1);

namespace RobertWesner\DependencyInjection\Tests\AutowireTestFixtures;

final class StaticProvider
{
    public static function provide(mixed $a, mixed $b): string
    {
        return 'static(' . $a . ', ' . $b . ')';
    }
}
