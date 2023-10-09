<?php

declare(strict_types=1);

namespace Poppables\Exception;

use InvalidArgumentException;
use Psr\Container\NotFoundExceptionInterface;

class UnknownIdentifier extends InvalidArgumentException implements NotFoundExceptionInterface
{
}
