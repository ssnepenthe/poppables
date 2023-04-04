<?php

declare(strict_types=1);

namespace Poppables;

use Pimple\Container;

final class Protect implements Poppable
{
    private $callable;

    public function __construct($callable)
    {
        $this->callable = Assert::invokable($callable);
    }

    public function pop(string $id, Container $pimple)
    {
        $pimple[$id] = $pimple->protect($this->callable);
    }
}
