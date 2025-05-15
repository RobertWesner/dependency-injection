<?php

namespace RobertWesner\DependencyInjection\Attributes;

use RobertWesner\DependencyInjection\Exception\AutowireException;

interface AutowireInterface
{
    /**
     * @throws AutowireException
     */
    public function resolve(): mixed;
}
