<?php

namespace Poppables\Exception;

use InvalidArgumentException;
use Psr\Container\NotFoundExceptionInterface;

class InvalidServiceIdentifier extends InvalidArgumentException implements NotFoundExceptionInterface
{
}
