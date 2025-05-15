<?php

namespace RobertWesner\DependencyInjection\Exception;

use Exception;
use Psr\Container\ContainerExceptionInterface;

class ContainerException extends Exception implements ContainerExceptionInterface {}
