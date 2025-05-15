<?php

declare(strict_types=1);

namespace RobertWesner\DependencyInjection\Exception;

use Psr\Container\NotFoundExceptionInterface;

class AutowireException extends ContainerException implements NotFoundExceptionInterface {}
