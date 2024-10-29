<?php

namespace PHPShots\Common;

use Closure;
use TypeError;
use PHPShots\Common\TypeAlias;

/**
 * Class BindIt
 *
 * An abstract class for implementing a dependency injection container.
 * This class allows for binding abstract types to concrete implementations,
 * handling method bindings, and managing shared instances.
 *
 * Version: 0.1.1
 */
abstract class BindIt extends TypeAlias implements BindItInterface
{
    /**
     * The container's bindings.
     *
     * @var array<string, array{concrete: Closure|string|null, shared: bool}>
     */
    protected $bindings = [];

    /**
     * The container's method bindings.
     *
     * @var array<string, Closure>
     */
    protected $methodBindings = [];

    /**
     * The rebound callbacks.
     *
     * @var array<string, Closure[]>
     */
    protected $reboundCallbacks = [];
    

    /**
     * The resolved instances.
     *
     * @var array<string, mixed>
     */
    protected $resolvedInstances = [];

    /**
     * Register a binding with the container.
     *
     * @param  string  $abstract
     * @param  \Closure|string|null  $concrete
     * @param  bool  $shared
     * @return void
     *
     * @throws TypeError
     *
     * @version 0.1.1
     */
    public function bind(string $abstract, \Closure|string|null $concrete = null, bool $shared = false): void
    {
        $this->dropStaleInstances($abstract);

        // If no concrete type was given, set it to the abstract type.
        $concrete = $concrete ?? $abstract;

        // Wrap the concrete type in a Closure if it's a string.
        if (is_string($concrete)) {
            $concrete = $this->getClosure($abstract, $concrete);
        } elseif (!$concrete instanceof Closure) {
            throw new TypeError(self::class . '::bind(): Argument #2 ($concrete) must be of type Closure|string|null');
        }

        $this->bindings[$abstract] = ['concrete' => $concrete, 'shared' => $shared];

        // If the abstract type was already resolved, fire the rebound listener.
        if ($this->resolved($abstract)) {
            $this->rebound($abstract);
        }
    }

    /**
     * Register a shared binding in the container.
     *
     * @param  string  $abstract
     * @param  \Closure|string|null  $concrete
     * @return void
     *
     * @version 0.1.1
     */
    public function singleton(string $abstract, \Closure|string|null $concrete = null): void
    {
        $this->bind($abstract, $concrete, true);
    }

    /**
     * Register a shared binding if it hasn't already been registered.
     *
     * @param  string  $abstract
     * @param  \Closure|string|null  $concrete
     * @return void
     */
    public function singletonIf($abstract, $concrete = null)
    {
        if (! $this->bound($abstract)) {
            $this->singleton($abstract, $concrete);
        }
    }

    /**
     * Register a binding if it hasn't already been registered.
     *
     * @param  string  $abstract
     * @param  \Closure|string|null  $concrete
     * @param  bool  $shared
     * @return void
     *
     * @version 0.1.1
     */
    public function bindIf(string $abstract, \Closure|string|null $concrete = null, bool $shared = false): void
    {
        if (!$this->bound($abstract)) {
            $this->bind($abstract, $concrete, $shared);
        }
    }

    /**
     * Bind a new callback to an abstract's rebind event.
     *
     * @param  string  $abstract
     * @param  Closure  $callback
     * @return mixed
     *
     * @version 0.1.1
     */
    public function rebinding(string $abstract, Closure $callback)
    {
        $this->reboundCallbacks[$abstract = $this->getAlias($abstract)][] = $callback;

        if ($this->bound($abstract)) {
            return $this->make($abstract);
        }
    }


    /**
     * Register a method binding with attributes.
     *
     * @param array|string $method
     * @param Closure $callback
     * @return void
     */
    public function bindMethodWithAttributes($method, Closure $callback): void
    {
        $methodKey = $this->parseBindMethod($method);
        $this->methodBindings[$methodKey] = $callback;

    }

    /**
     * Bind a callback to resolve with Container::call.
     *
     * @param  array|string  $method
     * @param  Closure  $callback
     * @return void
     *
     * @version 0.1.1
     */
    public function bindMethod($method, Closure $callback): void
    {
        $this->methodBindings[$this->parseBindMethod($method)] = $callback;
    }

   
    /**
     * Fire the "rebound" callbacks for the given abstract type.
     *
     * @param  string  $abstract
     * @return void
     */
    protected function rebound($abstract)
    {
        $instance = $this->make($abstract);

        foreach ($this->getReboundCallbacks($abstract) as $callback) {
            $callback($this, $instance);
        }
    }

    /**
     * Get the rebound callbacks for a given type.
     *
     * @param  string  $abstract
     * @return array
     */
    protected function getReboundCallbacks($abstract)
    {
        return $this->reboundCallbacks[$abstract] ?? [];
    }

     /**
     * Check if a binding exists.
     *
     * @param  string  $abstract
     * @return bool
     *
     * @version 0.1.1
     */
    public function bound(string $abstract): bool
    {
        return isset($this->bindings[$abstract]) ||
               isset($this->instances[$abstract]) ||
               $this->isAlias($abstract);
    }

    /**
     * Drop stale instances from the container.
     *
     * @param  string  $abstract
     * @return void
     *
     * @version 0.1.1
     */
    abstract protected function dropStaleInstances(string $abstract): void;

    /**
     * Get the Closure for the binding.
     *
     * @param  string  $abstract
     * @param  string  $concrete
     * @return Closure
     *
     * @version 0.1.1
     */
    abstract protected function getClosure(string $abstract, string $concrete): Closure;

    /**
     * Check if a binding has been resolved.
     *
     * @param  string  $abstract
     * @return bool
     *
     * @version 0.1.1
     */
    abstract public function resolved(string $abstract): bool;


    /**
     * Create an instance of a binding.
     *
     * @param string $abstract
     * @param array $parameters
     * @return mixed
     *
     * @version 0.1.1
     */
    abstract public function make(string $abstract, array $parameters = []): mixed;

    /**
     * Get the method binding for the given method.
     *
     * @param  string  $method
     * @param  mixed  $instance
     * @return mixed
     *
     * @version 0.1.1
     */
    public function callMethodBinding(string $method, $instance)
    {
        if (isset($this->methodBindings[$method])) {
            return call_user_func($this->methodBindings[$method], $instance, $this);
        }
        throw new \Exception("Method {$method} not bound.");
    }


    
    /**
     * Determine if the container has a method binding.
     *
     * @param  string  $method
     * @return bool
     *
     * @version 0.1.1
     */
    public function hasMethodBinding(string $method): bool
    {
        return isset($this->methodBindings[$method]);
    }

    /**
     * Get the container's bindings.
     *
     * @return array<string, array{concrete: Closure|string|null, shared: bool}>
     *
     * @version 0.1.1
     */
    public function getBindings(): array
    {
        return $this->bindings;
    }

    /**
     * Get the method to be bound in class@method format.
     *
     * @param  array|string  $method
     * @return string
     *
     * @version 0.1.1
     */
    protected function parseBindMethod($method): string
    {
        return is_array($method) ? $method[0] . '@' . $method[1] : $method;
    }
}