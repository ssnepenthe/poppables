<?php

namespace Poppables;

use Pimple\Container;
use Psr\Container\ContainerInterface;

final class DefinitionInvokable implements Wrapped
{
    private $callable;

    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }

    public function __invoke(Container $pimple)
    {
        return ($this->callable)($pimple[ContainerInterface::class]);
    }

    public function getCallable(): callable
    {
        return $this->callable;
    }
}
