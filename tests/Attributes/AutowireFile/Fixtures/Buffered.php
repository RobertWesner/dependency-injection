<?php

declare(strict_types=1);

namespace RobertWesner\DependencyInjection\Tests\Attributes\AutowireFile\Fixtures;

use RobertWesner\DependencyInjection\Attributes\AutowireFile;
use RobertWesner\DependencyInjection\Attributes\BufferFile;
use Stringable;

final readonly class Buffered implements Stringable
{
    public function __construct(
        #[AutowireFile(__DIR__ . '/foo.txt')]
        #[BufferFile]
        private string $content,
        #[AutowireFile(__DIR__ . '/foo.txt')]
        #[BufferFile]
        private string $contentAgain,
    ) {}

    public function __toString()
    {
        return $this->content . $this->contentAgain;
    }
}
