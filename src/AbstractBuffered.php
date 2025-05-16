<?php

declare(strict_types=1);

namespace RobertWesner\DependencyInjection;

abstract class AbstractBuffered
{
    private static Buffer $buffer;

    public function getBuffer(): Buffer
    {
        if (!isset(self::$buffer)) {
            self::$buffer = new Buffer();
        }

        return self::$buffer;
    }
}
