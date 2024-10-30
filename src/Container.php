<?php

namespace PHPShots\Common;

use Closure;
use PHPShots\Common\Traits\Build;
use PHPShots\Common\Traits\Contextual;
use PHPShots\Common\Interfaces\ContainerInterface;
use PHPShots\Common\Interfaces\ContextualBindingBuilderInterface;

/**
 * Class Container
 *
 * The `Container` class provides a dependency injection container for managing class instances
 * and dependencies. It supports registering shared services, binding contextual bindings, and
 * resolving instances while maintaining a singleton structure.
 *
 * @package PHPShots\Common
 * @version 0.1.1
 */
abstract class Container extends BindIt implements ContainerInterface, TypeAliasInterface
{
    use Contextual, Build;

    /**
     * The container's shared store for storing resolved instances.
     *
     * @var object[]
     */
    protected $store = [];

    /**
     * The current globally available instance of the container.
     *
     * @var static
     */
    protected static $instance;

    /**
     * The extension closures for services, allowing modification of resolved instances.
     *
     * @var array[]
     */
    protected $extenders = [];

    /**
     * Clears all stored instances from the container.
     *
     * @return void
     */
    public function forgetAllStore(): void
    {
        $this->store = [];
    }

    /**
     * Removes a specific resolved instance from the container's store.
     *
     * @param  string  $abstract  The abstract name of the service.
     * @return void
     */
    public function forgetStore($abstract): void
    {
        unset($this->store[$abstract]);
    }

    /**
     * Gets the globally available container instance.
     *
     * @return static
     */
    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static;
        }

        return static::$instance;
    }

    /**
     * Sets the shared container instance.
     *
     * @param  ContainerInterface|null  $container  The container instance to set.
     * @return self
     */
    public static function setInstance(?ContainerInterface $container = null): ContainerInterface
    {
        return static::$instance = $container;
    }

    /**
     * Removes all stored instances and aliases for a given abstract type.
     *
     * @param  string  $abstract  The abstract type to drop from the store.
     * @return void
     */
    protected function dropStore(string $abstract): void
    {
        unset($this->store[$abstract], $this->aliases[$abstract]);
    }

    /**
     * Registers an existing value as shared within the container.
     *
     * @param  string  $abstract  The abstract name of the service.
     * @param  mixed   $value     The instance or value to register.
     * @return mixed
     */
    public function store($abstract, $value): mixed
    {
        $this->removeAbstractAlias($abstract);
        $isBound = $this->bound($abstract);
        unset($this->aliases[$abstract]);

        $this->store[$abstract] = $value;

        if ($isBound) {
            $this->rebound($abstract);
        }

        return $value;
    }

    /**
     * Retrieves the extender callbacks for a given type.
     *
     * @param  string  $abstract  The abstract name of the service.
     * @return array
     */
    protected function getExtenders($abstract): array
    {
        return $this->extenders[$this->getAlias($abstract)] ?? [];
    }

    /**
     * Clears all extender callbacks for a given type.
     *
     * @param  string  $abstract  The abstract name of the service.
     * @return void
     */
    public function forgetExtenders($abstract): void
    {
        unset($this->extenders[$this->getAlias($abstract)]);
    }

    /**
     * Extends an abstract type in the container with a given closure.
     *
     * @param  string   $abstract  The abstract name of the service.
     * @param  Closure  $closure   The closure that extends the service.
     * @return void
     * @throws \InvalidArgumentException
     */
    public function extend($abstract, Closure $closure): void
    {
        $abstract = $this->getAlias($abstract);

        if (isset($this->store[$abstract])) {
            $this->store[$abstract] = $closure($this->store[$abstract], $this);
            $this->rebound($abstract);
        } else {
            $this->extenders[$abstract][] = $closure;

            if ($this->resolved($abstract)) {
                $this->rebound($abstract);
            }
        }
    }

    /**
     * Creates a contextual binding for a given type.
     *
     * @param  array|string  $concrete  The concrete class or array of classes.
     * @return ContextualBindingBuilderInterface
     */
    public function when($concrete): ContextualBindingBuilderInterface
    {
        $aliases = [];
        $concrete = is_array($concrete) ? $concrete : [$concrete];

        foreach ($concrete as $c) {
            $aliases[] = $this->getAlias($c);
        }

        return new ContextualBindingBuilder($this, $aliases);
    }

    /**
     * Checks if a given offset exists within the container.
     *
     * @param  string  $key  The key to check.
     * @return bool
     */
    public function offsetExists($key): bool
    {
        return $this->bound($key);
    }

    /**
     * Retrieves the value at a given offset.
     *
     * @param  string  $key  The key to retrieve.
     * @return mixed
     */
    public function offsetGet($key): mixed
    {
        return $this->make($key);
    }

    /**
     * Sets the value at a given offset.
     *
     * @param  string  $key    The key to set.
     * @param  mixed   $value  The value to store.
     * @return void
     */
    public function offsetSet($key, $value): void
    {
        $this->bind($key, $value instanceof Closure ? $value : fn() => $value);
    }

    /**
     * Unsets the value at a given offset.
     *
     * @param  string  $key  The key to unset.
     * @return void
     */
    public function offsetUnset($key): void
    {
        unset($this->bindings[$key], $this->store[$key], $this->resolved[$key]);
    }

    /**
     * Dynamically accesses container services.
     *
     * @param  string  $key  The key of the service to access.
     * @return mixed
     */
    public function __get($key)
    {
        return $this[$key];
    }

    /**
     * Dynamically sets container services.
     *
     * @param  string  $key    The key of the service.
     * @param  mixed   $value  The service instance or value.
     * @return void
     */
    public function __set($key, $value)
    {
        $this[$key] = $value;
    }

    /**
     * Checks if a given type is shared in the container.
     *
     * @param  string  $abstract  The abstract type to check.
     * @return bool
     */
    public function isShared($abstract): bool
    {
        return isset($this->store[$abstract]) ||
            (isset($this->bindings[$abstract]['shared']) &&
                $this->bindings[$abstract]['shared'] === true);
    }

    /**
     * Creates and retrieves a closure to resolve the given type from the container.
     *
     * @param  string  $abstract  The abstract type.
     * @return Closure
     */
    public function factory($abstract): Closure
    {
        return fn () => $this->make($abstract);
    }

    /**
     * Determines if the given abstract type has been resolved.
     *
     * @param  string  $abstract  The abstract type to check.
     * @return bool
     */
    public function resolved($abstract): bool
    {
        if ($this->isAlias($abstract)) {
            $abstract = $this->getAlias($abstract);
        }

        return isset($this->resolved[$abstract]) || isset($this->store[$abstract]);
    }
}
