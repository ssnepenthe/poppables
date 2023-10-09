<?php

declare(strict_types=1);

namespace Poppables;

use Pimple\Container;

final class Parameter implements Poppable
{
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function pop(string $id, Container $pimple)
    {
        $pimple[$id] = $this->value;
    }
}
