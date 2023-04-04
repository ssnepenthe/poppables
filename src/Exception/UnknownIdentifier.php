<?php

namespace Poppables\Exception;

use InvalidArgumentException;
use Psr\Container\NotFoundExceptionInterface;

class UnknownIdentifier extends InvalidArgumentException implements NotFoundExceptionInterface
{
}
