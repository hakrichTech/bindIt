<?php

namespace PHPShots\Common\Interfaces;

use ArrayAccess;
use Closure;

/**
 * Interface BindItInterface
 * 
 * @version: 0.1.1
 * 
 * This interface defines the structure for a dependency injection container,
 * enabling the registration, resolution, and management of bindings. It extends
 * ArrayAccess for convenient array-like access to container elements.
 */
interface BindItInterface extends ArrayAccess
{
    /**
     * Register a binding with the container.
     * 
     * This method registers an abstract type and its corresponding concrete 
     * implementation within the container.
     * 
     * @param  string          $abstract The abstract type identifier.
     * @param  Closure|string|null $concrete The concrete implementation or Closure.
     * @param  bool            $shared Whether to register as a shared instance.
     * @return void
     */
    public function bind(string $abstract, Closure|string|null $concrete = null, bool $shared = false): void;

    /**
     * Register a shared (singleton) binding in the container.
     * 
     * This method ensures that the same instance is resolved each time
     * the abstract type is requested.
     * 
     * @param  string          $abstract The abstract type identifier.
     * @param  Closure|string|null $concrete The concrete implementation or Closure.
     * @return void
     */
    public function singleton(string $abstract, Closure|string|null $concrete = null): void;

    /**
     * Register a binding if it hasn't already been registered.
     * 
     * Only registers the binding if the abstract type does not already exist
     * in the container, ensuring existing bindings remain unchanged.
     * 
     * @param  string          $abstract The abstract type identifier.
     * @param  Closure|string|null $concrete The concrete implementation or Closure.
     * @param  bool            $shared Whether to register as a shared instance.
     * @return void
     */
    public function bindIf(string $abstract, Closure|string|null $concrete = null, bool $shared = false): void;

    /**
     * Register a shared binding if it hasn't already been registered.
     * 
     * Registers the binding as a singleton only if the abstract type does not 
     * already exist in the container.
     * 
     * @param  string          $abstract The abstract type identifier.
     * @param  Closure|string|null $concrete The concrete implementation or Closure.
     * @return void
     */
    public function singletonIf(string $abstract, Closure|string|null $concrete = null): void;

    /**
     * Bind a new callback to an abstract's rebind event.
     * 
     * This allows for dynamic re-assignment of bindings. When the binding for
     * the specified abstract type is updated, the callback will be triggered.
     * 
     * @param  string          $abstract The abstract type identifier.
     * @param  Closure         $callback A closure to be executed on rebind.
     * @return mixed
     */
    public function rebinding(string $abstract, Closure $callback);

    /**
     * Bind a callback to resolve with `Container::call`.
     * 
     * Registers a callback for a specific method, enabling the container to 
     * handle complex resolutions for method calls.
     * 
     * @param  array|string    $method The method or array of methods to bind.
     * @param  Closure         $callback The callback to execute on method binding.
     * @return void
     */
    public function bindMethod(array|string $method, Closure $callback): void;

    /**
     * Check if a binding exists.
     * 
     * Determines if the specified abstract type is currently registered in 
     * the container.
     * 
     * @param  string          $abstract The abstract type identifier.
     * @return bool True if the binding exists, false otherwise.
     */
    public function bound(string $abstract): bool;

    /**
     * Get the method binding for the given method.
     * 
     * Retrieves the callback associated with a specific method, allowing 
     * custom behavior to be executed upon method resolution.
     * 
     * @param  string          $method The method identifier.
     * @param  mixed           $instance The instance associated with the binding.
     * @return mixed The result of the method binding callback.
     */
    public function callMethodBinding(string $method, mixed $instance);

    /**
     * Determine if the container has a method binding.
     * 
     * Checks if a specific method has a binding registered with the container.
     * 
     * @param  string          $method The method identifier.
     * @return bool True if the method binding exists, false otherwise.
     */
    public function hasMethodBinding(string $method): bool;

    /**
     * Get all bindings registered in the container.
     * 
     * Returns an array of all bindings, with each binding consisting of 
     * the concrete implementation and whether it is shared.
     * 
     * @return array<string, array{concrete: Closure|string|null, shared: bool}> 
     *         An associative array of bindings.
     */
    public function getBindings(): array;
}
