<?php

declare(strict_types=1);

namespace RobertWesner\DependencyInjection\Attributes;

use Attribute;
use JsonException;
use RobertWesner\DependencyInjection\AbstractBuffered;
use RobertWesner\DependencyInjection\Exception\AutowireException;

#[Attribute(Attribute::TARGET_PARAMETER)]
class AutowireJson extends AbstractBuffered implements FileBasedAutowireInterface
{
    public function __construct(
        private readonly string $filename,
        /**
         * $.foo.bar
         *
         * {
         *     foo: {
         *         bar: 1337
         *     }
         * }
         */
        private readonly string $path,
    ) {}

    public function resolve(bool $buffered = false): mixed
    {
        $segments = explode('.', $this->path);
        if (array_shift($segments) !== '$') {
            throw new AutowireException(sprintf(
                'Invalid JSON path "%s".',
                $this->path,
            ));
        }

        if (!file_exists($this->filename) && !$this->getBuffer()->has($this->filename)) {
            throw new AutowireException(sprintf(
                'Missing JSON file "%s".',
                $this->filename,
            ));
        }

        if ($buffered && $this->getBuffer()->has($this->filename)) {
            $result = $this->getBuffer()->get($this->filename);
        } else {
            try {
                $result = json_decode(file_get_contents($this->filename), true, flags: JSON_THROW_ON_ERROR);
                if ($buffered) {
                    $this->getBuffer()->set($this->filename, $result);
                }
            } catch (JsonException $exception) {
                throw new AutowireException(sprintf(
                    'Invalid JSON file "%s".',
                    $this->filename,
                ), previous: $exception);
            }
        }

        foreach ($segments as $segment) {
            if (!isset($result[$segment])) {
                throw new AutowireException(sprintf(
                    'Missing path "%s" in JSON file "%s".',
                    $this->path,
                    $this->filename,
                ));
            }

            $result = $result[$segment];
        }

        return $result;
    }
}
