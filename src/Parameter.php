<?php

namespace Poppables;

use Pimple\Container;

class Parameter implements Poppable
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
