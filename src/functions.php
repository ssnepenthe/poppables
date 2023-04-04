<?php

declare(strict_types=1);

namespace Poppables;

if (! function_exists(__NAMESPACE__ . '\\extend')) {
    function extend($callable)
    {
        return new Extend($callable);
    }
}

if (! function_exists(__NAMESPACE__ . '\\factory')) {
    function factory($callable)
    {
        return new Factory($callable);
    }
}

if (! function_exists(__NAMESPACE__ . '\\protect')) {
    function protect($callable)
    {
        return new Protect($callable);
    }
}
