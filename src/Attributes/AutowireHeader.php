<?php

declare(strict_types=1);

namespace RobertWesner\DependencyInjection\Attributes;

use Attribute;

/**
 * Will always return NULL on missing header so all usages should be nullable!
 */
#[Attribute(Attribute::TARGET_PARAMETER)]
readonly class AutowireHeader implements AutowireInterface
{
    public function __construct(
        private string $name,
    ) {}

    public function resolve(bool $buffered = false): ?string
    {
        return $_SERVER['HTTP_' . strtoupper(str_replace('-', '_', $this->name))] ?? null;
    }
}
