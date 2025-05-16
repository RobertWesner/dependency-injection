<?php

declare(strict_types=1);

namespace RobertWesner\DependencyInjection\Attributes;

use Attribute;
use JsonException;
use RobertWesner\DependencyInjection\AbstractBuffered;
use RobertWesner\DependencyInjection\Exception\AutowireException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

#[Attribute(Attribute::TARGET_PARAMETER)]
class AutowireYaml extends AbstractBuffered implements FileBasedAutowireInterface
{
    public function __construct(
        private readonly string $filename,
        /**
         * $.foo.bar
         *
         * foo:
         *     bar: 1337
         */
        private readonly string $path,
    ) {}

    public function resolve(bool $buffered = false): mixed
    {
        $segments = explode('.', $this->path);
        if (array_shift($segments) !== '$') {
            throw new AutowireException(sprintf(
                'Invalid YAML path "%s".',
                $this->path,
            ));
        }

        if (!file_exists($this->filename) && !$this->getBuffer()->has($this->filename)) {
            throw new AutowireException(sprintf(
                'Missing YAML file "%s".',
                $this->filename,
            ));
        }

        if ($buffered && $this->getBuffer()->has($this->filename)) {
            $result = $this->getBuffer()->get($this->filename);
        } else {
            try {
                $result = Yaml::parse(file_get_contents($this->filename));
                if ($buffered) {
                    $this->getBuffer()->set($this->filename, $result);
                }
            } catch (ParseException $exception) {
                throw new AutowireException(sprintf(
                    'Invalid YAML file "%s".',
                    $this->filename,
                ), previous: $exception);
            }
        }

        foreach ($segments as $segment) {
            if (!isset($result[$segment])) {
                throw new AutowireException(sprintf(
                    'Missing path "%s" in YAML file "%s".',
                    $this->path,
                    $this->filename,
                ));
            }

            $result = $result[$segment];
        }

        return $result;
    }
}
