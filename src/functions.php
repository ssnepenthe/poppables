<?php

declare(strict_types=1);

namespace Poppables;

if (! function_exists(__NAMESPACE__ . '\\alias')) {
    function alias(string $originalId)
    {
        return new Alias($originalId);
    }
}

if (! function_exists(__NAMESPACE__ . '\\extend')) {
    function extend($invokable)
    {
        return new Extend($invokable);
    }
}

if (! function_exists(__NAMESPACE__ . '\\factory')) {
    function factory($invokable)
    {
        return new Factory($invokable);
    }
}

if (! function_exists(__NAMESPACE__ . '\\protect')) {
    function protect($invokable)
    {
        return new Protect($invokable);
    }
}
