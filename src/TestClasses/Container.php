<?php
namespace PHPShots\Common\TestClasses;

use PHPShots\Common\BindIt;
use PHPShots\Common\BindItInterface;


/**
 * Class Container
 *
 * A dependency injection container that extends the BindIt abstract class.
 * This class provides implementations for abstract methods and manages
 * shared instances, bindings, and rebinding events.
 */
class Container extends BindIt
{
    /**
     * Check if a binding exists.
     *
     * @param string $abstract
     * @return bool
     */
    public function bound(string $abstract): bool
    {
        return isset($this->bindings[$abstract]) || isset($this->resolvedInstances[$abstract]);
    }

    /**
     * Drop stale instances from the container.
     *
     * @param string $abstract
     * @return void
     */
    protected function dropStaleInstances(string $abstract): void
    {
        unset($this->resolvedInstances[$abstract]);
    }

    /**
     * Get the Closure for the binding.
     *
     * @param string $abstract
     * @param string $concrete
     * @return Closure
     */
    protected function getClosure(string $abstract, string $concrete): \Closure
    {
        return function ($container) use ($abstract, $concrete) {
            return $container->build($concrete);
        };
    }

    /**
     * Check if a binding has been resolved.
     *
     * @param string $abstract
     * @return bool
     */
    protected function resolved(string $abstract): bool
    {
        return isset($this->resolvedInstances[$abstract]);
    }

    /**
     * Handle the rebinding of an abstract type.
     *
     * @param string $abstract
     * @return void
     */
    protected function rebound(string $abstract): void
    {
        foreach ($this->reboundCallbacks[$abstract] ?? [] as $callback) {
            $callback($this->make($abstract), $this);
        }
    }

    /**
     * Create an instance of a binding.
     *
     * @param string $abstract
     * @return mixed
     * @throws \Exception
     */
    public function make(string $abstract): mixed
    {
        if (isset($this->resolvedInstances[$abstract])) {
            return $this->resolvedInstances[$abstract];
        }

        if (!isset($this->bindings[$abstract])) {
            throw new \Exception("No binding registered for {$abstract}");
        }

        $concrete = $this->bindings[$abstract]['concrete'];
        $object = $concrete($this);

        if ($this->bindings[$abstract]['shared']) {
            $this->resolvedInstances[$abstract] = $object;
        }


        return $object;
    }

    

    /**
     * Get the alias of the abstract.
     *
     * @param string $abstract
     * @return string
     */
    protected function getAlias(string $abstract): string
    {
        return $abstract;
    }

    /**
     * Build an instance of the given concrete type.
     *
     * @param string $concrete
     * @return mixed
     */
    public function build(string $concrete): mixed
    {
        $reflectionClass = new \ReflectionClass($concrete);

        if (!$reflectionClass->isInstantiable()) {
            throw new \Exception("{$concrete} is not instantiable");
        }

        $constructor = $reflectionClass->getConstructor();

        if (!$constructor) {
            return new $concrete;
        }

        $parameters = $constructor->getParameters();
        $dependencies = array_map(function ($parameter) {
            return $this->resolveDependency($parameter);
        }, $parameters);

        return $reflectionClass->newInstanceArgs($dependencies);
    }

    /**
     * Resolve a dependency for a constructor parameter.
     *
     * @param \ReflectionParameter $parameter
     * @return mixed
     */
    protected function resolveDependency(\ReflectionParameter $parameter): mixed
    {
        $type = $parameter->getType();

        if (!$type || $type->isBuiltin()) {
            return $parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue() : null;
        }

        return $this->make($type->getName());
    }


    

   
}
