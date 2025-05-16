<?php

declare(strict_types=1);

namespace RobertWesner\DependencyInjection\Attributes;

use Attribute;
use RobertWesner\DependencyInjection\Exception\AutowireException;

#[Attribute(Attribute::TARGET_PARAMETER)]
class AutowireCallable implements AutowireInterface
{
    /**
     * @param callable $callable
     * @noinspection PhpMissingParamTypeInspection callable
     */
    public function __construct(
        private $callable,
        private readonly array $arguments = [],
    ) {}

    public function resolve(bool $buffered = false): mixed
    {
        if (!is_callable($this->callable)) {
            throw new AutowireException('Invalid argument passed to AutowireCallable.');
        }

        return ($this->callable)(...$this->arguments);
    }
}
