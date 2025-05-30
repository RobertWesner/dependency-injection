<?php

declare(strict_types=1);

namespace RobertWesner\DependencyInjection\Attributes;

use Attribute;
use Flow\JSONPath\JSONPath;
use Flow\JSONPath\JSONPathException;
use JsonException;
use RobertWesner\DependencyInjection\AbstractBuffered;
use RobertWesner\DependencyInjection\Exception\AutowireException;

#[Attribute(Attribute::TARGET_PARAMETER)]
class AutowireJson extends AbstractBuffered implements FileBasedAutowireInterface
{
    public function __construct(
        private readonly string $filename,
        private readonly string $path,
        private readonly bool $multiple = false,
    ) {}

    public function resolve(bool $buffered = false): mixed
    {
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

        try {
            $result = new JSONPath($result)->find($this->path)->getData();
        } catch (JSONPathException $exception) {
            throw new AutowireException(sprintf(
                'Invalid JSON path "%s".',
                $this->path,
            ), previous: $exception);
        }

        if (!$this->multiple) {
            return $result[0];
        }

        return $result;
    }
}
