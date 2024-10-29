<?php

namespace PHPShots\Common;

use PHPShots\Common\ContextualBindingBuilderInterface;

class ContextualBindingBuilder implements ContextualBindingBuilderInterface
{
    /**
     * The underlying container instance.
     *
     * @var Container
     */
    protected $container;

    /**
     * The concrete instance.
     *
     * @var string|array
     */
    protected $concrete;

    /**
     * The abstract target.
     *
     * @var string
     */
    protected $needs;

    /**
     * Create a new contextual binding builder.
     *
     * @param  Container  $container
     * @param  string|array  $concrete
     * @return void
     */
    public function __construct(Container $container, $concrete)
    {
        $this->concrete = $concrete;
        $this->container = $container;
    }

    /**
     * Define the abstract target that depends on the context.
     *
     * @param  string  $abstract
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
     * @param  \Closure|string|array  $implementation
     * @return void
     */
    public function give($implementation)
    {
        $concretes = is_array($this->concrete)? $this->concrete :[$this->concrete];

        foreach ($concretes as $concrete) {
            $this->container->addContextualBinding($concrete, $this->needs, $implementation);
        }
    }
    /**
     * Specify the configuration item to bind as a primitive.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return void
     */
    public function giveConfig($key, $default = null)
    {
        $this->give(fn ($container) => $container->get('config')->get($key, $default));
    }
}
