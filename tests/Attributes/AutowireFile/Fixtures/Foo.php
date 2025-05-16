<?php

declare(strict_types=1);

namespace RobertWesner\DependencyInjection\Tests\Attributes\AutowireFile\Fixtures;

use RobertWesner\DependencyInjection\Attributes\AutowireFile;
use Stringable;

final readonly class Foo implements Stringable
{
    public function __construct(
        #[AutowireFile(__DIR__ . '/foo.txt')]
        private string $content,
    ) {}

    public function __toString()
    {
        return $this->content;
    }
}
