<?php

namespace Poppables\Exception;

use Psr\Container\ContainerExceptionInterface;
use RuntimeException;

class FrozenService extends RuntimeException implements ContainerExceptionInterface
{
}
