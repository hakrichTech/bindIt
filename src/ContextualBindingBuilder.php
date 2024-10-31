<?php

namespace PHPShots\Common;

use PHPShots\Common\Interfaces\ContextualBindingBuilderInterface;

/**
 * Class ContextualBindingBuilder
 *
 * Facilitates the creation of contextual bindings within a container,
 * allowing the definition of specific implementations based on the context
 * in which a given abstract type is resolved.
 */
class ContextualBindingBuilder implements ContextualBindingBuilderInterface
{
    /**
     * The underlying container instance responsible for managing bindings.
     *
     * @var Container
     */
    protected $container;

    /**
     * The concrete instance or instances associated with the binding.
     *
     * @var string|array
     */
    protected $concrete;

    /**
     * The abstract type that is dependent on the context.
     *
     * @var string
     */
    protected $needs;

    /**
     * Create a new contextual binding builder.
     *
     * @param  Container  $container  The container instance to use for bindings.
     * @param  string|array  $concrete  The concrete type(s) to be bound.
     */
    public function __construct(Container $container, $concrete)
    {
        $this->concrete = $concrete;
        $this->container = $container;
    }

    /**
     * Define the abstract target that depends on the context.
     *
     * @param  string  $abstract  The abstract type to bind.
     * @return $this
     */
    public function needs($abstract)
    {
        $this->needs = $abstract;

        return $this;
    }

    /**
     * Define the implementation for the contextual binding.
     *
     * @param  \Closure|string|array  $implementation  The implementation to bind.
     * @return void
     */
    public function give($implementation)
    {
        // Ensure $concrete is always treated as an array.
        $concretes = is_array($this->concrete) ? $this->concrete : [$this->concrete];

        // Add the contextual binding for each concrete type.
        foreach ($concretes as $concrete) {
            $this->container->addContextualBinding($concrete, $this->needs, $implementation);
        }
    }

    /**
     * Specify the configuration item to bind as a primitive.
     *
     * This method allows for the binding of a configuration value as the
     * implementation for the contextual binding.
     *
     * @param  string  $key      The configuration key to bind.
     * @param  mixed   $default  The default value if the configuration key does not exist.
     * @return void
     */
    public function giveConfig($key, $default = null)
    {
        $this->give(fn ($container) => $container->get('config')[$key]);
    }
}
