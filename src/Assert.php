<?php

declare(strict_types=1);

namespace Poppables;

use Poppables\Exception\ExpectedInvokable;

final class Assert
{
    public static function invokable($value, string $descriptor = 'Value')
    {
        if (! (is_object($value) && method_exists($value, '__invoke'))) {
            throw new ExpectedInvokable("{$descriptor} is not a Closure or invokable object.");
        }

        return $value;
    }
}
