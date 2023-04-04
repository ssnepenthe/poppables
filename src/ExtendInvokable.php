<?php

namespace Poppables;

use Pimple\Container;
use Psr\Container\ContainerInterface;

class ExtendInvokable implements Wrapped
{
    private $invokable;

    public function __construct($invokable)
    {
        $this->invokable = Assert::invokable($invokable);
    }

    public function __invoke($resolved, Container $pimple)
    {
        return ($this->invokable)($resolved, $pimple[ContainerInterface::class]);
    }

    public function getInvokable()
    {
        return $this->invokable;
    }
}
