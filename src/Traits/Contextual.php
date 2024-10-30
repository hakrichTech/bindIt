<?php

namespace PHPShots\Common\Traits;

/**
 * Trait Contextual
 *
 * Provides support for contextual bindings within a container, allowing the binding
 * of specific implementations based on the class being resolved. Enables customization
 * of concrete implementations for different parts of an application.
 * @version 0.1.1
 */
trait Contextual
{
    /**
     * Holds a map of contextual bindings where concrete implementations are associated with specific
     * abstract types, organized by context.
     *
     * @var array[]
     */
    public $contextual = [];

    /**
     * A stack representing the current concretions being built, used to manage
     * the binding context during the instantiation process.
     *
     * @var array[]
     */
    protected $buildStack = [];

    /**
     * Retrieve the contextual concrete binding for a given abstract type.
     *
     * This method checks if a concrete implementation is contextually bound to an abstract type.
     * If no binding is found for the specified abstract type, it will attempt to retrieve bindings
     * associated with any alias of the abstract type.
     *
     * @param  string|callable  $abstract  The abstract type or callable to retrieve the contextual binding for.
     * @return \Closure|string|array|null  The binding associated with the abstract, or null if not found.
     */
    protected function getContextualConcrete($abstract)
    {
        if (!is_null($binding = $this->findInContextualBindings($abstract))) {
            return $binding;
        }

        // Check for aliases of the given abstract type, iterating through each alias
        // to determine if any contextual binding is associated with it.
        if (empty($this->abstractAliases[$abstract])) {
            return null;
        }

        foreach ($this->abstractAliases[$abstract] as $alias) {
            if (!is_null($binding = $this->findInContextualBindings($alias))) {
                return $binding;
            }
        }
        
        return null;
    }

    /**
     * Locate the concrete binding for a given abstract type within the contextual binding array.
     *
     * @param  string|callable  $abstract  The abstract type or callable to locate.
     * @return \Closure|string|null        The concrete binding if found, or null otherwise.
     */
    protected function findInContextualBindings($abstract)
    {
        return $this->contextual[end($this->buildStack)][$abstract] ?? null;
    }

    /**
     * Add a new contextual binding to the container.
     *
     * This method allows associating a specific implementation for a given concrete type,
     * for use only in the specified context.
     *
     * @param  string  $concrete            The concrete class or interface.
     * @param  string  $abstract            The abstract class or interface being bound.
     * @param  \Closure|string  $implementation  The concrete implementation or closure.
     * @return void
     */
    public function addContextualBinding($concrete, $abstract, $implementation): void
    {
        $this->contextual[$concrete][$this->getAlias($abstract)] = $implementation;
    }
}
