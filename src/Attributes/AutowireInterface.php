<?php

declare(strict_types=1);

namespace RobertWesner\DependencyInjection\Attributes;

use RobertWesner\DependencyInjection\Exception\AutowireException;

interface AutowireInterface
{
    /**
     * @throws AutowireException
     */
    public function resolve(bool $buffered = false): mixed;
}
