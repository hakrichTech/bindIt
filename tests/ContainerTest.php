<?php

namespace Tests\Common;

use PHPUnit\Framework\TestCase;
use PHPShots\Common\Container; // Assuming you have a Container class
use PHPShots\Common\ContextualBindingBuilder;
use PHPShots\Common\Traits\Contextual;

class ContainerTest extends TestCase
{
    protected $container;

    protected function setUp(): void
    {
        $this->container = new Container(); // Initialize your container
    }

    public function testBindAndMake()
    {
        $this->container->bind('AbstractClass', 'ConcreteClass');

        $resolved = $this->container->make('AbstractClass');
        $this->assertInstanceOf('ConcreteClass', $resolved);
    }

    public function testSingletonBinding()
    {
        $this->container->singleton('SingletonClass', 'ConcreteClass');

        $instance1 = $this->container->make('SingletonClass');
        $instance2 = $this->container->make('SingletonClass');

        $this->assertSame($instance1, $instance2); // Both instances should be the same
    }

    public function testContextualBinding()
    {
        $contextualBindingBuilder = new ContextualBindingBuilder($this->container, 'ConcreteClass');
        $contextualBindingBuilder->needs('AbstractClass')->give('ConcreteImplementation');

        // Assume the make method respects contextual bindings
        $resolved = $this->container->make('ConcreteClass');
        $this->assertEquals('ConcreteImplementation', $resolved);
    }

    public function testAddContextualBinding()
    {
        $this->container->addContextualBinding('ConcreteClass', 'AbstractClass', 'ConcreteImplementation');

        $resolved = $this->container->make('ConcreteClass');
        $this->assertEquals('ConcreteImplementation', $resolved);
    }

    public function testFindInContextualBindings()
    {
        $this->container->addContextualBinding('ConcreteClass', 'AbstractClass', 'ConcreteImplementation');

        $binding = $this->container->findInContextualBindings('AbstractClass');
        $this->assertEquals('ConcreteImplementation', $binding);
    }

    public function testMultipleConcreteBindings()
    {
        $this->container->addContextualBinding('ConcreteClass1', 'AbstractClass', 'ConcreteImplementation');
        $this->container->addContextualBinding('ConcreteClass2', 'AbstractClass', 'ConcreteImplementation');

        $this->assertEquals('ConcreteImplementation', $this->container->make('ConcreteClass1'));
        $this->assertEquals('ConcreteImplementation', $this->container->make('ConcreteClass2'));
    }

    public function testResolvingWithDependencies()
    {
        // Assuming ConcreteClass has a dependency on AbstractClass
        $this->container->bind('AbstractClass', 'ConcreteImplementation');
        $this->container->bind('ConcreteClass', 'ClassWithDependency');

        $resolved = $this->container->make('ConcreteClass');
        $this->assertInstanceOf('ClassWithDependency', $resolved);
    }

    public function testConfigBinding()
    {
        // Mock the configuration
        $this->container->set('config', ['key' => 'configValue']);

        $contextualBindingBuilder = new ContextualBindingBuilder($this->container, 'ConcreteClass');
        $contextualBindingBuilder->giveConfig('key', 'defaultValue');

        $resolved = $this->container->make('ConcreteClass');
        $this->assertEquals('configValue', $resolved);
    }

    public function testContextualConcreteFallback()
    {
        $this->container->addContextualBinding('ConcreteClass', 'AbstractClass', 'ConcreteImplementation');

        $binding = $this->container->getContextualConcrete('AnotherConcreteClass'); // No binding should return null
        $this->assertNull($binding);
    }
}
