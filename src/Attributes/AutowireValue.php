<?php

declare(strict_types=1);

namespace RobertWesner\DependencyInjection\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
readonly class AutowireValue implements AutowireInterface
{
    public function __construct(
        private mixed $value,
    ) {}

    public function resolve(bool $buffered = false): mixed
    {
        return $this->value;
    }
}
