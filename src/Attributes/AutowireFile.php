<?php

declare(strict_types=1);

namespace RobertWesner\DependencyInjection\Attributes;

use Attribute;
use RobertWesner\DependencyInjection\AbstractBuffered;
use RobertWesner\DependencyInjection\Exception\AutowireException;

#[Attribute(Attribute::TARGET_PARAMETER)]
class AutowireFile extends AbstractBuffered implements FileBasedAutowireInterface
{
    public function __construct(
        private readonly string $filename,
    ) {}

    public function resolve(bool $buffered = false): mixed
    {
        if (!file_exists($this->filename) && !$this->getBuffer()->has($this->filename)) {
            throw new AutowireException(sprintf(
                'Missing file "%s".',
                $this->filename,
            ));
        }

        if ($buffered && $this->getBuffer()->has($this->filename)) {
            $content = $this->getBuffer()->get($this->filename);
        } else {
            $content = file_get_contents($this->filename);
            if ($buffered) {
                $this->getBuffer()->set($this->filename, $content);
            }
        }

        return $content;
    }
}
