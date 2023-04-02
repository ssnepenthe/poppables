<?php

declare(strict_types=1);

namespace Poppables;

use Pimple\Container;

final class Definition implements Poppable
{
    private $callable;

    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }

    public function pop(string $id, Container $pimple)
    {
        $pimple[$id] = new DefinitionInvokable($this->callable);
    }
}
