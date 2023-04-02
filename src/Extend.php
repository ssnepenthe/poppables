<?php

declare(strict_types=1);

namespace Poppables;

use Pimple\Container;

final class Extend implements Poppable
{
    private $callable;

    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }

    public function pop(string $id, Container $pimple)
    {
        $pimple->extend($id, new ExtendInvokable($this->callable));
    }
}
