<?php

/*
 * Adpapted from Pimple tests.
 *
 * Copyright (c) 2009 Fabien Potencier
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Poppables\Tests;

use PHPUnit\Framework\TestCase;
use Poppables\Container;
use Poppables\Exception\ExpectedInvokable;
use Poppables\Exception\FrozenService;
use Poppables\Exception\InvalidServiceIdentifier;
use Poppables\Exception\UnknownIdentifier;
use Poppables\ServiceProvider;
use Psr\Container\ContainerInterface;

use function Poppables\extend;
use function Poppables\factory;
use function Poppables\protect;

class ContainerPimpleCompatibilityTest extends TestCase
{
    public function testWithString()
    {
        $container = new Container();
        $container->set('param', 'value');

        $this->assertEquals('value', $container->get('param'));
    }

    public function testWithClosure()
    {
        $container = new Container();
        $container->set('service', function () {
            return new Fixtures\Service();
        });

        $this->assertInstanceOf(Fixtures\Service::class, $container->get('service'));
    }

    public function testServicesShouldBeDifferent()
    {
        $container = new Container();
        $container->set('service', factory(function () {
            return new Fixtures\Service();
        }));

        $serviceOne = $container->get('service');
        $this->assertInstanceOf(Fixtures\Service::class, $serviceOne);

        $serviceTwo = $container->get('service');
        $this->assertInstanceOf(Fixtures\Service::class, $serviceTwo);

        $this->assertNotSame($serviceOne, $serviceTwo);
    }

    public function testShouldPassContainerAsParameter()
    {
        $container = new Container();
        $container->set('service', function () {
            return new Fixtures\Service();
        });
        $container->set('container', function ($container) {
            return $container;
        });

        $this->assertNotSame($container, $container->get('service'));
        $this->assertSame($container, $container->get('container'));
    }

    public function testHas()
    {
        $container = new Container();
        $container->set('param', 'value');
        $container->set('service', function () {
            return new Fixtures\Service();
        });

        $container->set('null', null);

        $this->assertTrue($container->has('param'));
        $this->assertTrue($container->has('service'));
        $this->assertTrue($container->has('null'));
        $this->assertFalse($container->has('non_existent'));
    }

    public function testConstructorInjection()
    {
        $params = ['param' => 'value'];
        $container = new Container($params);

        $this->assertSame($params['param'], $container->get('param'));
    }

    public function testGetValidatesKeyIsPresent()
    {
        $this->expectException(UnknownIdentifier::class);
        $this->expectExceptionMessage('Identifier "foo" is not defined.');

        $container = new Container();
        echo $container->get('foo');
    }

    /**
     * @group legacy
     */
    public function testLegacyGetValidatesKeyIsPresent()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Identifier "foo" is not defined.');

        $container = new Container();
        echo $container->get('foo');
    }

    public function testGetHonorsNullValues()
    {
        $container = new Container();
        $container->set('foo', null);
        $this->assertNull($container->get('foo'));
    }

    public function testUnset()
    {
        $container = new Container();
        $container->set('param', 'value');
        $container->set('service', function () {
            return new Fixtures\Service();
        });

        $container->unset('param');
        $container->unset('service');

        $this->assertFalse($container->has('param'));
        $this->assertFalse($container->has('service'));
    }

    /**
     * @dataProvider serviceDefinitionProvider
     */
    public function testShare($service)
    {
        $container= new Container();
        $container->set('shared_service', $service);

        $serviceOne = $container->get('shared_service');
        $this->assertInstanceOf(Fixtures\Service::class, $serviceOne);

        $serviceTwo = $container->get('shared_service');
        $this->assertInstanceOf(Fixtures\Service::class, $serviceTwo);

        $this->assertSame($serviceOne, $serviceTwo);
    }

    /**
     * @dataProvider serviceDefinitionProvider
     */
    public function testProtect($service)
    {
        $container = new Container();
        $container->set('protected', protect($service));

        $this->assertSame($service, $container->get('protected'));
    }

    public function testGlobalFunctionNameAsParameterValue()
    {
        $container = new Container();
        $container->set('global_function', 'strlen');
        $this->assertSame('strlen', $container->get('global_function'));
    }

    public function testRaw()
    {
        $container = new Container();
        $container->set('service', factory($definition = function () {
            return 'foo';
        }));
        $this->assertSame($definition, $container->raw('service'));
    }

    public function testRawHonorsNullValues()
    {
        $container = new Container();
        $container->set('foo', null);
        $this->assertNull($container->raw('foo'));
    }

    public function testFluentRegister()
    {
        $container = new Container();
        $this->assertSame($container, $container->register($this->getMockBuilder(ServiceProvider::class)->getMock()));
    }

    public function testRawValidatesKeyIsPresent()
    {
        $this->expectException(UnknownIdentifier::class);
        $this->expectExceptionMessage('Identifier "foo" is not defined.');

        $container = new Container();
        $container->raw('foo');
    }

    /**
     * @group legacy
     */
    public function testLegacyRawValidatesKeyIsPresent()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Identifier "foo" is not defined.');

        $container = new Container();
        $container->raw('foo');
    }

    /**
     * @dataProvider serviceDefinitionProvider
     */
    public function testExtend($service)
    {
        $container = new Container();
        $container->set('shared_service', function () {
            return new Fixtures\Service();
        });
        $container->set('factory_service', factory(function () {
            return new Fixtures\Service();
        }));

        $container->set('shared_service', extend($service));
        $serviceOne = $container->get('shared_service');
        $this->assertInstanceOf(Fixtures\Service::class, $serviceOne);
        $serviceTwo = $container->get('shared_service');
        $this->assertInstanceOf(Fixtures\Service::class, $serviceTwo);
        $this->assertSame($serviceOne, $serviceTwo);
        $this->assertSame($serviceOne->value, $serviceTwo->value);

        $container->set('factory_service', extend($service));
        $serviceOne = $container->get('factory_service');
        $this->assertInstanceOf(Fixtures\Service::class, $serviceOne);
        $serviceTwo = $container->get('factory_service');
        $this->assertInstanceOf(Fixtures\Service::class, $serviceTwo);
        $this->assertNotSame($serviceOne, $serviceTwo);
        $this->assertNotSame($serviceOne->value, $serviceTwo->value);
    }

    public function testExtendValidatesKeyIsPresent()
    {
        $this->expectException(UnknownIdentifier::class);
        $this->expectExceptionMessage('Identifier "foo" is not defined.');

        $container = new Container();
        $container->set('foo', extend(function () {
        }));
    }

    /**
     * @group legacy
     */
    public function testLegacyExtendValidatesKeyIsPresent()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Identifier "foo" is not defined.');

        $container = new Container();
        $container->set('foo', extend(function () {
        }));
    }

    public function testKeys()
    {
        $container = new Container();
        $container->set('foo', 123);
        $container->set('bar', 123);

        // ContainerInterface is automatically added to all container instances.
        $this->assertEquals([ContainerInterface::class, 'foo', 'bar'], $container->keys());
    }

    /** @test */
    public function settingAnInvokableObjectShouldTreatItAsFactory()
    {
        $container = new Container();
        $container->set('invokable', new Fixtures\Invokable());

        $this->assertInstanceOf(Fixtures\Service::class, $container->get('invokable'));
    }

    /** @test */
    public function settingNonInvokableObjectShouldTreatItAsParameter()
    {
        $container = new Container();
        $container->set('non_invokable', new Fixtures\NonInvokable());

        $this->assertInstanceOf('Poppables\Tests\Fixtures\NonInvokable', $container->get('non_invokable'));
    }

    /**
     * @dataProvider badServiceDefinitionProvider
     */
    public function testFactoryFailsForInvalidServiceDefinitions($service)
    {
        $this->expectException(ExpectedInvokable::class);
        $this->expectExceptionMessage('Service definition is not a Closure or invokable object.');

        factory($service);
    }

    /**
     * @group legacy
     * @dataProvider badServiceDefinitionProvider
     */
    public function testLegacyFactoryFailsForInvalidServiceDefinitions($service)
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Service definition is not a Closure or invokable object.');

        factory($service);
    }

    /**
     * @dataProvider badServiceDefinitionProvider
     */
    public function testProtectFailsForInvalidServiceDefinitions($service)
    {
        $this->expectException(ExpectedInvokable::class);
        $this->expectExceptionMessage('Callable is not a Closure or invokable object.');

        protect($service);
    }

    /**
     * @group legacy
     * @dataProvider badServiceDefinitionProvider
     */
    public function testLegacyProtectFailsForInvalidServiceDefinitions($service)
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Callable is not a Closure or invokable object.');

        protect($service);
    }

    /**
     * @dataProvider badServiceDefinitionProvider
     */
    public function testExtendFailsForKeysNotContainingServiceDefinitions($service)
    {
        $this->expectException(InvalidServiceIdentifier::class);
        $this->expectExceptionMessage('Identifier "foo" does not contain an object definition.');

        $container = new Container();
        $container->set('foo', $service);
        $container->set('foo', extend(function () {
        }));
    }

    /**
     * @group legacy
     * @dataProvider badServiceDefinitionProvider
     */
    public function testLegacyExtendFailsForKeysNotContainingServiceDefinitions($service)
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Identifier "foo" does not contain an object definition.');

        $container = new Container();
        $container->set('foo', $service);
        $container->set('foo', extend(function () {
        }));
    }

    /**
     * @group legacy
     * @expectedDeprecation How Pimple behaves when extending protected closures will be fixed in Pimple 4. Are you sure "foo" should be protected?
     */
    public function testExtendingProtectedClosureDeprecation()
    {
        $container = new Container();
        $container->set('foo', protect(function () {
            return 'bar';
        }));

        $container->set('foo', extend(function ($value) {
            return $value.'-baz';
        }));

        $this->assertSame('bar-baz', $container->get('foo'));
    }

    /**
     * @dataProvider badServiceDefinitionProvider
     */
    public function testExtendFailsForInvalidServiceDefinitions($service)
    {
        $this->expectException(ExpectedInvokable::class);
        $this->expectExceptionMessage('Extension service definition is not a Closure or invokable object.');

        $container = new Container();
        $container->set('foo', function () {
        });
        $container->set('foo', extend($service));
    }

    /**
     * @group legacy
     * @dataProvider badServiceDefinitionProvider
     */
    public function testLegacyExtendFailsForInvalidServiceDefinitions($service)
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Extension service definition is not a Closure or invokable object.');

        $container = new Container();
        $container->set('foo', function () {
        });
        $container->set('foo', extend($service));
    }

    public function testExtendFailsIfFrozenServiceIsNonInvokable()
    {
        $this->expectException(FrozenService::class);
        $this->expectExceptionMessage('Cannot override frozen service "foo".');

        $container = new Container();
        $container->set('foo', function () {
            return new Fixtures\NonInvokable();
        });
        $foo = $container->get('foo');

        $container->set('foo', extend(function () {
        }));
    }

    public function testExtendFailsIfFrozenServiceIsInvokable()
    {
        $this->expectException(FrozenService::class);
        $this->expectExceptionMessage('Cannot override frozen service "foo".');

        $container = new Container();
        $container->set('foo', function () {
            return new Fixtures\Invokable();
        });
        $foo = $container->get('foo');

        $container->set('foo', extend(function () {
        }));
    }

    /**
     * Provider for invalid service definitions.
     */
    public function badServiceDefinitionProvider()
    {
        return [
          [123],
          [new Fixtures\NonInvokable()],
        ];
    }

    /**
     * Provider for service definitions.
     */
    public function serviceDefinitionProvider()
    {
        return [
            [function ($value) {
                $service = new Fixtures\Service();
                $service->value = $value;

                return $service;
            }],
            [new Fixtures\Invokable()],
        ];
    }

    public function testDefiningNewServiceAfterFreeze()
    {
        $container = new Container();
        $container->set('foo', function () {
            return 'foo';
        });
        $foo = $container->get('foo');

        $container->set('bar', function () {
            return 'bar';
        });
        $this->assertSame('bar', $container->get('bar'));
    }

    public function testOverridingServiceAfterFreeze()
    {
        $this->expectException(FrozenService::class);
        $this->expectExceptionMessage('Cannot override frozen service "foo".');

        $container = new Container();
        $container->set('foo', function () {
            return 'foo';
        });
        $foo = $container->get('foo');

        $container->set('foo', function () {
            return 'bar';
        });
    }

    /**
     * @group legacy
     */
    public function testLegacyOverridingServiceAfterFreeze()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Cannot override frozen service "foo".');

        $container = new Container();
        $container->set('foo', function () {
            return 'foo';
        });
        $foo = $container->get('foo');

        $container->set('foo', function () {
            return 'bar';
        });
    }

    public function testRemovingServiceAfterFreeze()
    {
        $container = new Container();
        $container->set('foo', function () {
            return 'foo';
        });
        $foo = $container->get('foo');

        $container->unset('foo');
        $container->set('foo', function () {
            return 'bar';
        });
        $this->assertSame('bar', $container->get('foo'));
    }

    public function testExtendingService()
    {
        $container = new Container();
        $container->set('foo', function () {
            return 'foo';
        });
        $container->set('foo', extend(function ($foo, $app) {
            return "$foo.bar";
        }));
        $container->set('foo', extend(function ($foo, $app) {
            return "$foo.baz";
        }));
        $this->assertSame('foo.bar.baz', $container->get('foo'));
    }

    public function testExtendingServiceAfterOtherServiceFreeze()
    {
        $container = new Container();
        $container->set('foo', function () {
            return 'foo';
        });
        $container->set('bar', function () {
            return 'bar';
        });
        $foo = $container->get('foo');

        $container->set('bar', extend(function ($bar, $app) {
            return "$bar.baz";
        }));
        $this->assertSame('bar.baz', $container->get('bar'));
    }
}
