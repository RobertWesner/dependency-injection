<?php

declare(strict_types=1);

namespace RobertWesner\DependencyInjection\Attributes;

use Attribute;
use Dotenv\Dotenv;
use Dotenv\Exception\InvalidFileException;
use RobertWesner\DependencyInjection\Exception\AutowireException;

#[Attribute(Attribute::TARGET_PARAMETER)]
readonly class AutowireEnv implements AutowireInterface
{
    public function __construct(
        private string $filename,
        private string $key,
    ) {}

    public function resolve(): mixed
    {
        if (!file_exists($this->filename)) {
            throw new AutowireException(sprintf(
                'Missing .env file "%s".',
                $this->filename,
            ));
        }

        try {
            $content = Dotenv::parse(file_get_contents($this->filename)) ?: [];
            if (!isset($content[$this->key])) {
                throw new AutowireException(sprintf(
                    'Missing key "%s" in .env file "%s".',
                    $this->key,
                    $this->filename,
                ));
            }

            return $content[$this->key];
        } catch (InvalidFileException $exception) {
            throw new AutowireException(sprintf(
                'Invalid .env file "%s".',
                $this->filename,
            ), previous: $exception);
        }
    }
}
