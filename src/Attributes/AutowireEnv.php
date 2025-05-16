<?php

declare(strict_types=1);

namespace RobertWesner\DependencyInjection\Attributes;

use Attribute;
use Dotenv\Dotenv;
use Dotenv\Exception\InvalidFileException;
use RobertWesner\DependencyInjection\AbstractBuffered;
use RobertWesner\DependencyInjection\Exception\AutowireException;

#[Attribute(Attribute::TARGET_PARAMETER)]
class AutowireEnv extends AbstractBuffered implements FileBasedAutowireInterface
{
    public function __construct(
        private readonly string $filename,
        private readonly string $key,
    ) {}

    public function resolve(bool $buffered = false): mixed
    {
        if (!file_exists($this->filename) && !$this->getBuffer()->has($this->filename)) {
            throw new AutowireException(sprintf(
                'Missing .env file "%s".',
                $this->filename,
            ));
        }

        if ($buffered && $this->getBuffer()->has($this->filename)) {
            $content = $this->getBuffer()->get($this->filename);
        } else {
            try {
                $content = Dotenv::parse(file_get_contents($this->filename)) ?: [];
                if ($buffered) {
                    $this->getBuffer()->set($this->filename, $content);
                }
            } catch (InvalidFileException $exception) {
                throw new AutowireException(sprintf(
                    'Invalid .env file "%s".',
                    $this->filename,
                ), previous: $exception);
            }
        }

        if (!isset($content[$this->key])) {
            throw new AutowireException(sprintf(
                'Missing key "%s" in .env file "%s".',
                $this->key,
                $this->filename,
            ));
        }

        return $content[$this->key];
    }
}
