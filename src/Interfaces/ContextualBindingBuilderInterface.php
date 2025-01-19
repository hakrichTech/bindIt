<?php

namespace PHPShots\Common\Interfaces;

interface ContextualBindingBuilderInterface
{
    /**
     * Define the abstract target that depends on the context.
     *
     * @param  string  $abstract
     * @return $this
     */
    public function needs($abstract);

    /**
     * Define the implementation for the contextual binding.
     *
     * @param  \Closure|string|array  $implementation
     * @return void
     */
    public function give($implementation);

    /**
     * Specify the configuration item to bind as a primitive.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return void
     */
    public function giveConfig($key, $default = null);
}
