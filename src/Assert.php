<?php

namespace Poppables;

use InvalidArgumentException;

final class Assert
{
    public static function invokable($value)
    {
        if (! (is_object($value) && method_exists($value, '__invoke'))) {
            throw new InvalidArgumentException('@todo This should probably be a custom validation exception or similar');
        }

        return $value;
    }
}
