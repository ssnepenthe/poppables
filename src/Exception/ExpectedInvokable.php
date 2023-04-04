<?php

namespace Poppables\Exception;

use InvalidArgumentException;
use Psr\Container\ContainerExceptionInterface;

class ExpectedInvokable extends InvalidArgumentException implements ContainerExceptionInterface
{
}
