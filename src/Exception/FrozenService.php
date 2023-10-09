<?php

declare(strict_types=1);

namespace Poppables\Exception;

use Psr\Container\ContainerExceptionInterface;
use RuntimeException;

class FrozenService extends RuntimeException implements ContainerExceptionInterface
{
}
