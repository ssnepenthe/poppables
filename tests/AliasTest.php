<?php

declare(strict_types=1);

namespace Poppables\Tests;

use PHPUnit\Framework\TestCase;
use Poppables\Container;

use function Poppables\alias;

class AliasTest extends TestCase
{
    public function testAlias()
    {
        $container = new Container();

        $container->set('key', 'value');
        $container->set('alias-key', alias('key'));

        $this->assertTrue($container->has('alias-key'));
        $this->assertSame('value', $container->get('alias-key'));
    }

    public function testAliasIsStoredAsFactory()
    {
        $container = new Container();

        $container->set('key', 0);
        $container->set('alias-key', alias('key'));

        $this->assertSame(0, $container->get('key'));
        $this->assertSame(0, $container->get('alias-key'));

        $container->set('key', 1);

        $this->assertSame(1, $container->get('key'));
        $this->assertSame(1, $container->get('alias-key'));
    }
}
