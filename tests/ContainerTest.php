<?php

namespace Tests\Common;

use PHPUnit\Framework\TestCase;
use PHPShots\Common\ContextualBindingBuilder;
use PHPShots\Common\TestClasses\AbstractClass;
use PHPShots\Common\TestClasses\ConcreteClass;
use PHPShots\Common\TestClasses\ConcreteClass1;
use PHPShots\Common\TestClasses\ConcreteClass2;
use PHPShots\Common\TestClasses\GreetingService;
use PHPShots\Common\TestClasses\ConditionalService;
use PHPShots\Common\TestClasses\ClassWithDependency;
use PHPShots\Common\TestClasses\ConcreteImplementation;
use PHPShots\Common\Container; // Assuming you have a Container class

class ContainerTest extends TestCase
{
    protected $container;

    protected function setUp(): void
    {
        $this->container = new Container(); // Initialize your container
    }

    public function testBindAndMake()
    {
        $this->container->bind("ConditionalService",  function ($container){
            return new ConditionalService();
        });

        $resolved = $this->container->make('ConditionalService');
        $this->assertInstanceOf(ConditionalService::class, $resolved);
    }

    public function testSingletonBinding()
    {
        $this->container->singleton('GreetingService', GreetingService::class);

        $instance1 = $this->container->make('GreetingService');
        $instance2 = $this->container->make('GreetingService');

        $this->assertSame($instance1, $instance2); // Both instances should be the same
    }

    public function testContextualBinding()
    {
        $contextualBindingBuilder = new ContextualBindingBuilder($this->container, ConcreteClass::class);
        $contextualBindingBuilder->needs(AbstractClass::class)->give(ConcreteImplementation::class);

        $resolved = $this->container->make(ConcreteClass::class);
        $this->assertInstanceOf(ConcreteImplementation::class, $resolved->getDependency());
    }

    public function testAddContextualBinding()
    {
        $this->container->addContextualBinding(ConcreteClass::class, AbstractClass::class, ConcreteImplementation::class);
        
        $resolved = $this->container->make(ConcreteClass::class);
        $this->assertInstanceOf(ConcreteImplementation::class, $resolved->getDependency());
    }


    public function testMultipleConcreteBindings()
    {
        $this->container->addContextualBinding(ConcreteClass1::class, AbstractClass::class, ConcreteImplementation::class);
        $this->container->addContextualBinding(ConcreteClass2::class, AbstractClass::class, ConcreteImplementation::class);

        $resolved1 = $this->container->make(ConcreteClass1::class);
        $resolved2 = $this->container->make(ConcreteClass2::class);

        $this->assertInstanceOf(ConcreteImplementation::class, $resolved1->getDependency());
        $this->assertInstanceOf(ConcreteImplementation::class, $resolved2->getDependency());
    }

    public function testResolvingWithDependencies()
    {
        // Assuming ConcreteClass has a dependency on AbstractClass
        $this->container->bind(AbstractClass::class, ConcreteImplementation::class);
        $this->container->bind(ConcreteClass::class, ClassWithDependency::class);

        $resolved = $this->container->make(ConcreteClass::class);
        $this->assertInstanceOf(ClassWithDependency::class, $resolved);
        $this->assertInstanceOf(ConcreteImplementation::class, $resolved->getDependency());
    }

    // public function testConfigBinding()
    // {
    //     // Mock the configuration
    //     $this->container->store('config', ['key' => 'configValue']);

    //     $contextualBindingBuilder = new ContextualBindingBuilder($this->container, ConcreteClass::class);
    //     $contextualBindingBuilder->needs('config')->giveConfig('key', 'defaultValue');

    //     $resolved = $this->container->make(ConcreteClass::class);
    //     $this->assertEquals('configValue', $resolved->getConfig());
    // }

   
}
