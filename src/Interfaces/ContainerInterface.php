<?php

namespace PHPShots\Common\Interfaces;

/**
 * Interface ContainerInterface
 *
 * Defines the core methods required for dependency injection and instance management
 * within a container, including creating, binding, and retrieving instances.
 */
interface ContainerInterface
{
    /**
     * Get the globally available instance of the container.
     *
     * @return static|null Returns the current instance of the container or null if not set.
     */
    public static function getInstance();

    /**
     * Set the globally available instance of the container.
     *
     * @param ContainerInterface|null $container The container instance to set as globally accessible.
     * @return void
     */
    public static function setInstance(?ContainerInterface $container = null);

    /**
     * Resolve and return an instance for the specified type.
     *
     * @param string $abstract The abstract type or alias to resolve.
     * @param array $parameters Parameters to pass for resolution if needed.
     * @return mixed Returns the resolved instance.
     * @throws BindingResolutionException If the binding cannot be resolved.
     */
    public function make($abstract, array $parameters = []);

    /**
     * Register a shared instance for the given abstract type.
     *
     * @param string $abstract The abstract type to bind the instance to.
     * @param mixed $instance The instance to register in the container.
     * @return mixed Returns the registered instance.
     */
    public function instance($abstract, $instance);

    /**
     * Clear all bound instances from the container.
     *
     * This method allows for flushing the container of all registered singletons and instances,
     * useful in testing environments where fresh state is required.
     *
     * @return void
     */
    public function forgetInstances();

    /**
     * Remove a specific instance from the container.
     *
     * This method allows for the removal of a particular singleton or instance by its abstract type.
     *
     * @param string $abstract The abstract type or alias to remove from the container.
     * @return void
     */
    public function forgetInstance($abstract);

    /**
     * Clear all registered extenders for the container.
     *
     * This is useful when the container’s extensions need to be reset.
     *
     * @return void
     */
    public function forgetExtenders();

    /**
     * Extend a specific binding with additional functionality.
     *
     * Allows adding logic to an existing resolved instance or shared binding.
     *
     * @param string $abstract The abstract type to extend.
     * @param \Closure $closure A closure that modifies or extends the instance.
     * @return void
     */
    public function extend($abstract, \Closure $closure);

    /**
     * Define conditional binding based on a specific context.
     *
     * Allows defining bindings based on conditional rules, typically for dependency injection in specific scenarios.
     *
     * @param string $concrete The class or interface that should apply in the given context.
     * @return \PHPShots\Common\Interfaces\ContextualBindingBuilderInterface
     */
    public function when($concrete);

    /**
     * Determine if a binding for the abstract type exists.
     *
     * Checks if a particular type or alias has been bound in the container.
     *
     * @param string $abstract The abstract type to check.
     * @return bool Returns true if the binding exists, false otherwise.
     */
    public function has($abstract);

    /**
     * Determine if a particular abstract type is registered as a singleton.
     *
     * Checks if the binding is marked as shared or singleton within the container.
     *
     * @param string $abstract The abstract type to check.
     * @return bool Returns true if the binding is a singleton, false otherwise.
     */
    public function isShared($abstract);

    /**
     * Generate a new instance of the given abstract type.
     *
     * This is similar to `make` but always returns a new instance, bypassing any singleton or shared behavior.
     *
     * @param string $abstract The abstract type to create a new instance for.
     * @return mixed Returns the new instance.
     */
    public function factory($abstract);

    /**
     * Resolve the given abstract type from the container.
     *
     * This method performs the actual retrieval of an instance from the container.
     *
     * @param string $abstract The abstract type or alias to resolve.
     * @param array $parameters Optional parameters to use during resolution.
     * @return mixed Returns the resolved instance.
     * @throws BindingResolutionException If the binding cannot be resolved.
     */
    public function resolve($abstract, array $parameters = []);
}
