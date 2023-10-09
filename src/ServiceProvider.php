<?php

declare(strict_types=1);

namespace Poppables;

interface ServiceProvider
{
    public function register(Container $container);
}
