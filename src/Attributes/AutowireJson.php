<?php

namespace RobertWesner\DependencyInjection\Attributes;

use Attribute;
use JsonException;
use RobertWesner\DependencyInjection\Exception\AutowireException;

#[Attribute(Attribute::TARGET_PARAMETER)]
readonly class AutowireJson implements AutowireInterface
{
    public function __construct(
        private string $filename,
        /**
         * $.foo.bar
         *
         * {
         *     foo: {
         *         bar: 1337
         *     }
         * }
         */
        private string $path,
    ) {}

    public function resolve(): mixed
    {
        $segments = explode('.', $this->path);
        if (array_shift($segments) !== '$') {
            throw new AutowireException(sprintf(
                'Invalid JSON path "%s".',
                $this->path,
            ));
        }

        if (!file_exists($this->filename)) {
            throw new AutowireException(sprintf(
                'Missing JSON file "%s".',
                $this->filename,
            ));
        }

        // TODO: cache JSON content at runtime
        try {
            $result = json_decode(file_get_contents($this->filename), true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new AutowireException(sprintf(
                'Invalid JSON file "%s".',
                $this->filename,
            ), previous: $exception);
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
