<?php

declare(strict_types=1);

namespace Poppables;

use Pimple\Container;

final class Extend implements Poppable
{
    private $invokable;

    public function __construct($invokable)
    {
        $this->invokable = Assert::invokable($invokable, 'Extension service definition');
    }

    public function pop(string $id, Container $pimple)
    {
        $pimple->extend($id, new ExtendInvokable($this->invokable));
    }
}
