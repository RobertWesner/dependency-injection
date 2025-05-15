<?php

namespace RobertWesner\DependencyInjection\Exception;

use Psr\Container\NotFoundExceptionInterface;

class NotFoundException extends ContainerException implements NotFoundExceptionInterface {}
