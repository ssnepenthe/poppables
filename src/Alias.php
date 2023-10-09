<?php

declare(strict_types=1);

namespace Poppables;

use Pimple\Container;

final class Alias implements Poppable
{
    private $originalId;

    public function __construct(string $originalId)
    {
        $this->originalId = $originalId;
    }

    public function pop(string $id, Container $pimple)
    {
        $pimple[$id] = $pimple->factory(function (Container $pimple) {
            return $pimple[$this->originalId];
        });
    }
}
