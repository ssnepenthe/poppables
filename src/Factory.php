<?php

declare(strict_types=1);

namespace Poppables;

use Pimple\Container;

final class Factory implements Poppable
{
    private $invokable;

    public function __construct($invokable)
    {
        $this->invokable = Assert::invokable($invokable, 'Service definition');
    }

    public function pop(string $id, Container $pimple)
    {
        $pimple[$id] = $pimple->factory(new DefinitionInvokable($this->invokable));
    }
}
