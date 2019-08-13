<?php

declare(strict_types=1);

namespace Rescue\Tests\Container;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionException;
use Rescue\Container\Container;

final class ContainerTest extends TestCase
{
    /**
     * @throws ContainerExceptionInterface
     * @throws ReflectionException
     */
    public function testContainerAlias(): void
    {
        $container = new Container();

        $container->add('foo', Foo::class);

        $this->assertTrue($container->has('foo'));
        $this->assertInstanceOf(Foo::class, $container->get('foo'));
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws ReflectionException
     */
    public function testParams(): void
    {
        $container = new Container();

        $container->add(Foo::class);
        $container->add(Bar::class, Bar::class, [Foo::class]);

        $this->assertTrue($container->has(Foo::class));
        $this->assertTrue($container->has(Bar::class));

        $this->assertInstanceOf(Foo::class, $container->get(Foo::class));
        $this->assertInstanceOf(Bar::class, $container->get(Bar::Class));

        /** @var Bar $bar */
        $bar = $container->get(Bar::class);
        $this->assertInstanceOf(Foo::class, $bar->foo);
    }

    /**
     * @throws ReflectionException
     * @throws ContainerExceptionInterface
     */
    public function testParamsInstance(): void
    {
        $container = new Container();

        $container->add(Bar::class, Bar::class, [new Foo()]);

        $this->assertTrue($container->has(Bar::class));

        $this->assertInstanceOf(Bar::class, $container->get(Bar::Class));

        /** @var Bar $bar */
        $bar = $container->get(Bar::class);
        $this->assertInstanceOf(Foo::class, $bar->foo);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws ReflectionException
     */
    public function testContainerAutoParams(): void
    {
        $container = new Container();

        $container->add(Foo::class);
        $container->add(Bar::class);

        $this->assertTrue($container->has(Foo::class));
        $this->assertTrue($container->has(Bar::class));

        $this->assertInstanceOf(Foo::class, $container->get(Foo::class));
        $this->assertInstanceOf(Bar::class, $container->get(Bar::Class));

        /** @var Bar $bar */
        $bar = $container->get(Bar::class);
        $this->assertInstanceOf(Foo::class, $bar->foo);
    }

    /**
     * @throws ReflectionException
     * @throws ContainerExceptionInterface
     */
    public function testContainerAutoParamsAndInstance(): void
    {
        $container = new Container();

        /** @var Bar $bar */
        $bar = $container->add(Bar::class);

        $this->assertInstanceOf(Bar::class, $bar);
        $this->assertInstanceOf(Foo::class, $bar->foo);
        $this->assertInstanceOf(Foo::class, $container->get(Foo::class));
    }

    /**
     * @throws ContainerExceptionInterface
     */
    public function testNotFoundException(): void
    {
        $container = new Container();

        $this->expectException(NotFoundExceptionInterface::class);

        $container->get('abcd');
    }

    /**
     * @throws ReflectionException
     * @throws ContainerExceptionInterface
     */
    public function testAutoInterfaceAdd(): void
    {
        $container = new Container();
        $container->add(TestInterface::class, TestClass::class);
        $test2 = $container->add(TestClass2::class);

        $this->assertInstanceOf(TestClass2::class, $test2);
        $this->assertInstanceOf(TestInterface::class, $test2->test);
        $this->assertInstanceOf(TestClass::class, $container->get(TestInterface::class));
    }

    /**
     * @throws ReflectionException
     * @throws ContainerExceptionInterface
     */
    public function testNullableParams(): void
    {
        $container = new Container();

        /** @var TestClass3 $instance */
        $instance = $container->add(TestClass3::class);

        $this->assertInstanceOf(TestClass3::class, $container->get(TestClass3::class));
        $this->assertInstanceOf(Foo::class, $instance->foo);
        $this->assertNull($instance->bar);
    }

    /**
     * @throws ReflectionException
     * @throws ContainerExceptionInterface
     */
    public function testNullableParamsWithParams(): void
    {
        $container = new Container();

        /** @var TestClass3 $instance */
        $instance = $container->add(TestClass3::class, TestClass3::class, [
            new Foo(),
            new Bar(new Foo()),
        ]);

        $this->assertInstanceOf(TestClass3::class, $container->get(TestClass3::class));
        $this->assertInstanceOf(Foo::class, $instance->foo);
        $this->assertInstanceOf(Bar::class, $instance->bar);
    }

    /**
     * @throws ReflectionException
     */
    public function testSimple(): void
    {
        $container = new Container();

        /** @var TestClass4 $instance */
        $instance = $container->add(TestClass4::class, TestClass4::class, ['asdf']);

        $this->assertEquals('asdf', $instance->foo);

        /** @var TestClass5 $instance */
        $instance = $container->add(TestClass5::class, TestClass5::class, [false]);

        $this->assertEquals(false, $instance->foo);

        /** @var TestClass6 $instance */
        $instance = $container->add(TestClass6::class, TestClass6::class, [45]);

        $this->assertEquals(45, $instance->foo);
    }


    /**
     * @throws ContainerExceptionInterface
     */
    public function testAddByCallback(): void
    {
        $container = new Container();
        $container->addInstance('test', static function () {
            return new Foo();
        });
        $instance = $container->get('test');
        $this->assertInstanceOf(Foo::class, $instance);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws ReflectionException
     */
    public function testAddByCallbackAndGetFromContainer(): void
    {
        $container = new Container();
        $container->add(Foo::class);
        $container->addInstance(Bar::class, static function (Container $container) {
            return new Bar($container->get(Foo::class));
        });
        $instance = $container->get(Bar::class);
        $this->assertInstanceOf(Bar::class, $instance);
    }

    public function testAddInstance(): void
    {
        $container = new Container();
        $foo = new Foo();
        $instance = $container->addInstance(Foo::class, $foo);
        $this->assertEquals($foo, $instance);
    }
}

interface TestInterface
{
}


class Foo
{
}

class Bar
{
    public $foo;

    public function __construct(Foo $foo)
    {
        $this->foo = $foo;
    }
}

class TestClass implements TestInterface
{
}

class TestClass2
{
    public $test;

    public function __construct(TestInterface $test)
    {
        $this->test = $test;
    }
}


class TestClass3
{
    public $foo;
    public $bar;

    public function __construct(Foo $foo, Bar $bar = null)
    {
        $this->foo = $foo;
        $this->bar = $bar;
    }
}

class TestClass4
{
    public $foo;

    public function __construct(string $foo)
    {
        $this->foo = $foo;
    }
}

class TestClass5
{
    public $foo;

    public function __construct(bool $foo)
    {
        $this->foo = $foo;
    }
}

class TestClass6
{
    public $foo;

    public function __construct(int $foo)
    {
        $this->foo = $foo;
    }
}
