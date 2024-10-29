<?php

namespace PHPShots\Common\Tests;

use PHPUnit\Framework\TestCase;
use PHPShots\Common\TestClasses\Container;
use PHPShots\Common\TestClasses\GreetingService;
use PHPShots\Common\TestClasses\SingletonService;
use PHPShots\Common\TestClasses\OriginalService;
use PHPShots\Common\TestClasses\ReboundService;
use PHPShots\Common\TestClasses\ConditionalService;

class BindItTest extends TestCase
{
    protected Container $container;

    protected function setUp(): void
    {
        $this->container = new Container();
    }

    public function testBindAndMake()
    {
        $this->container->bind('greetingService', function() {
            return new GreetingService();
        });

        $service = $this->container->make('greetingService');
        $this->assertInstanceOf(GreetingService::class, $service);
        $this->assertEquals('Hello, World!', $service->greet());
    }

    public function testSingletonBinding()
    {
        $this->container->singleton('singletonService', function() {
            return new SingletonService();
        });

        $firstInstance = $this->container->make('singletonService');
        $secondInstance = $this->container->make('singletonService');

        $this->assertSame($firstInstance, $secondInstance);
        $this->assertEquals('Singleton Service!', $firstInstance->greet());
    }

    public function testRebinding()
    {
        $this->container->bind('rebindingService', function() {
            return new OriginalService();
        });

        // Make the service to check the initial binding
        $service = $this->container->make('rebindingService');
        $this->assertEquals('Original Service!', $service->greet());

        // Now, rebinding to a new service
        $this->container->bind('rebindingService', function() {
            return new ReboundService();
        });

        // Make the service again to get the rebound version
        $service = $this->container->make('rebindingService');
        $this->assertEquals('Rebound Service!', $service->greet());
    }

    public function testBindIf()
    {
        $this->container->bindIf('conditionalService', function() {
            return new ConditionalService();
        });

        // Should bind the service
        $this->assertTrue($this->container->bound('conditionalService'));

        // Should not bind again
        $this->container->bindIf('conditionalService', function() {
            return 'Should not bind again!';
        });

        $service = $this->container->make('conditionalService');
        
        $this->assertEquals('Should be bound!', $service->message());
    }
}