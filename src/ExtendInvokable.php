<?php

namespace Poppables;

use Pimple\Container;
use Psr\Container\ContainerInterface;

class ExtendInvokable implements Wrapped
{
    private $callable;

    public function __construct($callable)
    {
        $this->callable = Assert::invokable($callable);
    }

    public function __invoke($resolved, Container $pimple)
    {
        return ($this->callable)($resolved, $pimple[ContainerInterface::class]);
    }

    public function getCallable()
    {
        return $this->callable;
    }
}
