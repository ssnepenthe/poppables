<?php

declare(strict_types=1);

namespace Poppables\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Poppables\Container;
use Psr\Container\ContainerInterface;

class ContainerTest extends TestCase
{
    public function testConstructorThrowsForValuesArrayContainingContainerInterface()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Values array must not contain an entry for ' . ContainerInterface::class);

        new Container([ContainerInterface::class => 'doesntmatter']);
    }

    public function testSetDoesNotAllowSettingValueForContainerInterface()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot set value for reserved identifier ' . ContainerInterface::class);

        $container = new Container();
        $container->set(ContainerInterface::class, 'doesntmatter');
    }

    public function testUnsetDoesNotAllowUnsettingValueForContainerInterface()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot unset value for reserved identifier ' . ContainerInterface::class);

        $container = new Container();
        $container->unset(ContainerInterface::class);
    }
}
