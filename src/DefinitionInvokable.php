<?php

declare(strict_types=1);

namespace Poppables;

use Pimple\Container;
use Psr\Container\ContainerInterface;

final class DefinitionInvokable implements Wrapped
{
    private $invokable;

    public function __construct($invokable)
    {
        $this->invokable = Assert::invokable($invokable);
    }

    public function __invoke(Container $pimple)
    {
        return ($this->invokable)($pimple[ContainerInterface::class]);
    }

    public function getInvokable()
    {
        return $this->invokable;
    }
}
