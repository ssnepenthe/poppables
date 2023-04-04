<?php

declare(strict_types=1);

namespace Poppables;

use Pimple\Container;

final class Definition implements Poppable
{
    private $invokable;

    public function __construct($invokable)
    {
        $this->invokable = Assert::invokable($invokable);
    }

    public function pop(string $id, Container $pimple)
    {
        $pimple[$id] = new DefinitionInvokable($this->invokable);
    }
}
