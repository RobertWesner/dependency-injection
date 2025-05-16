<?php

namespace RobertWesner\DependencyInjection;

class Buffer
{
    private array $buffer = [];

    public function set(string $id, mixed $value): void
    {
        $this->buffer[$id] = $value;
    }

    public function has(string $id): bool
    {
        return isset($this->buffer[$id]);
    }

    public function get(string $id): mixed
    {
        return $this->buffer[$id] ?? null;
    }
}
