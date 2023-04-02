<?php

namespace Poppables;

use Pimple\Container;
use Psr\Container\ContainerInterface;

class ExtendInvokable implements Invokable
{
    private $callable;

    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }

    public function __invoke($resolved, Container $pimple)
    {
        return ($this->callable)($resolved, $pimple[ContainerInterface::class]);
    }

    public function getCallable(): callable
    {
        return $this->callable;
    }
}
