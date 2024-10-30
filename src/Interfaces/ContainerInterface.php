<?php

namespace PHPShots\Common\Interfaces;

use Closure;
use PHPShots\Common\Exceptions\BindingResolutionException;

/**
 * Interface ContainerInterface
 * 
 * This interface defines the core contract for a service container, responsible for 
 * managing class dependencies and performing dependency injection. The `ContainerInterface` 
 * facilitates the binding and resolution of services, allowing for the dynamic and 
 * contextual instantiation of classes. Key methods include handling singleton instances, 
 * resolving services with parameters, and managing bindings across different contexts.
 * 
 * @version 0.1.1
 */
interface ContainerInterface extends BindItInterface
{
    /**
     * Retrieve the globally available instance of the container.
     * 
     * @return static The globally shared container instance.
     */
    public static function getInstance();

    /**
     * Set the shared instance of the container.
     * 
     * @param  ContainerInterface|null  $container
     * @return ContainerInterface The current instance of the container.
     */
    public static function setInstance(?ContainerInterface $container = null): ContainerInterface;

    /**
     * Remove all stored bindings and instances from the container.
     * 
     * This method clears the entire container of bindings and shared instances, 
     * resetting the container to an empty state.
     * 
     * @return void
     */
    public function forgetAllStore(): void;

    /**
     * Remove a specific resolved binding from the container cache.
     * 
     * This method deletes a specific binding from the container, allowing it to be
     * re-bound or re-resolved when needed.
     * 
     * @param  string  $abstract  The unique identifier for the binding.
     * @return void
     */
    public function forgetStore(string $abstract): void;

    /**
     * Instantiate a concrete instance of the given type.
     * 
     * @param  Closure|string  $concrete The concrete type or a Closure to build the instance.
     * @return mixed The created instance of the specified type.
     * 
     * @throws BindingResolutionException If there is an issue resolving dependencies.
     * @throws CircularDependencyException If a circular dependency is detected.
     */
    public function build(Closure|string $concrete): mixed;

    /**
     * Add a contextual binding to the container.
     * 
     * This method sets up a binding for a specific type in a specific context, 
     * enabling unique implementations based on runtime requirements.
     * 
     * @param  string  $concrete The concrete class for the contextual binding.
     * @param  string  $abstract The abstract type to bind.
     * @param  Closure|string  $implementation The concrete implementation to use.
     * @return void
     */
    public function addContextualBinding(string $concrete, string $abstract, Closure|string $implementation): void;

    /**
     * Drop stale bindings and aliases for a specific type.
     * 
     * Removes bindings or aliases for a given type, ensuring that it can be re-bound or re-resolved.
     * 
     * @param  string  $abstract The identifier of the type to drop.
     * @return void
     */
    public function dropStore(string $abstract): void;

    /**
     * Register an existing instance as shared in the container.
     * 
     * This method stores a pre-existing instance in the container, ensuring it is shared 
     * across future resolutions.
     * 
     * @param  string  $abstract The identifier for the shared instance.
     * @param  mixed   $instance The instance to register as shared.
     * @return mixed The stored instance.
     */
    public function store(string $abstract, $instance): mixed;

    /**
     * Remove all extender callbacks for a specific type.
     * 
     * Clears all registered extenders for a particular type, resetting its instantiation logic.
     * 
     * @param  string  $abstract The identifier for the type.
     * @return void
     */
    public function forgetExtenders(string $abstract): void;

    /**
     * Extend an abstract type in the container with a closure.
     * 
     * Adds an extender to modify the resolution process for a specific abstract type.
     * 
     * @param  string   $abstract The identifier for the abstract type.
     * @param  Closure  $closure A closure to modify the resolution process.
     * @return void
     */
    public function extend(string $abstract, Closure $closure): void;

    /**
     * Define a contextual binding for a concrete implementation.
     * 
     * Specifies a binding that applies only in a certain context, determined by the provided type.
     * 
     * @param  array|string  $concrete The concrete type or array of types to apply the binding.
     * @return ContextualBindingBuilderInterface A builder for further contextual binding configuration.
     */
    public function when(array|string $concrete): ContextualBindingBuilderInterface;

    /**
     * Determine if a given item is present in the container.
     * 
     * Checks if the specified item has been bound or registered within the container.
     * 
     * @param  string  $id The identifier of the item to check.
     * @return bool True if the item is in the container, false otherwise.
     */
    public function has(string $id): bool;

    /**
     * Determine if a specific abstract type is shared in the container.
     * 
     * Checks if the specified abstract type is stored as a singleton or shared instance.
     * 
     * @param  string  $abstract The identifier of the abstract type.
     * @return bool True if the type is shared, false otherwise.
     */
    public function isShared(string $abstract): bool;

    /**
     * Get a closure to resolve a type from the container.
     * 
     * Returns a factory closure that can be used to create instances of the specified type.
     * 
     * @param  string  $abstract The identifier of the type.
     * @return Closure The factory closure for the type.
     */
    public function factory(string $abstract): Closure;

    /**
     * Create an instance of a binding.
     * 
     * Resolves and returns an instance of the specified binding, injecting any dependencies.
     * 
     * @param string $abstract The identifier of the type to resolve.
     * @param array $parameters Optional parameters for the resolution process.
     * @return mixed The resolved instance.
     * 
     * @throws BindingResolutionException If there is an issue resolving the binding.
     */
    public function make(string $abstract, array $parameters = []): mixed;

    /**
     * Determine if a given abstract type has been resolved.
     * 
     * Checks if the specified abstract type has been previously resolved and is cached.
     * 
     * @param  string  $abstract The identifier of the type.
     * @return bool True if the type has been resolved, false otherwise.
     */
    public function resolved(string $abstract): bool;
}
