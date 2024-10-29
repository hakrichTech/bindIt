<?php

namespace PHPShots\Common;

/**
 * Interface BindItInterface
 *
 * An interface for implementing a dependency injection container.
 * This interface outlines the methods required for binding,
 * resolving, and managing dependencies.
 *
 * Version: 0.1.1
 */
interface BindItInterface
{
    /**
     * Register a binding with the container.
     *
     * @param  string  $abstract
     * @param  \Closure|string|null  $concrete
     * @param  bool  $shared
     * @return void
     *
     * @version 0.1.1
     */
    public function bind(string $abstract, \Closure|string|null $concrete = null, bool $shared = false): void;

    /**
     * Register a shared binding in the container.
     *
     * @param  string  $abstract
     * @param  \Closure|string|null  $concrete
     * @return void
     *
     * @version 0.1.1
     */
    public function singleton(string $abstract, \Closure|string|null $concrete = null): void;

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
    public function bindIf(string $abstract, \Closure|string|null $concrete = null, bool $shared = false): void;

    /**
     * Bind a new callback to an abstract's rebind event.
     *
     * @param  string  $abstract
     * @param  \Closure  $callback
     * @return mixed
     *
     * @version 0.1.1
     */
    public function rebinding(string $abstract, \Closure $callback);

    /**
     * Bind a callback to resolve with Container::call.
     *
     * @param  array|string  $method
     * @param  \Closure  $callback
     * @return void
     *
     * @version 0.1.1
     */
    public function bindMethod($method, \Closure $callback): void;

    /**
     * Check if a binding exists.
     *
     * @param  string  $abstract
     * @return bool
     *
     * @version 0.1.1
     */
    public function bound(string $abstract): bool;

    /**
     * Create an instance of a binding.
     *
     * @param string $abstract
     * @param array $parameters
     * @return mixed
     *
     * @throws BindingResolutionException
     * @version 0.1.1
     */
    public function make(string $abstract, array $parameters = []): mixed;

    /**
     * Get the method binding for the given method.
     *
     * @param  string  $method
     * @param  mixed  $instance
     * @return mixed
     *
     * @version 0.1.1
     */
    public function callMethodBinding(string $method, $instance);

    /**
     * Determine if the container has a method binding.
     *
     * @param  string  $method
     * @return bool
     *
     * @version 0.1.1
     */
    public function hasMethodBinding(string $method): bool;

    /**
     * Get the container's bindings.
     *
     * @return array<string, array{concrete: \Closure|string|null, shared: bool}>
     *
     * @version 0.1.1
     */
    public function getBindings(): array;
}
