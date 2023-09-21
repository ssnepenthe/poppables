<?php

declare(strict_types=1);

namespace Poppables;

use Pimple\Container;

interface Poppable
{
    public function pop(string $id, Container $pimple);
}
