<?php
namespace PHPShots\Common\Traits;

use Closure;
use ReflectionClass;
use ReflectionFunction;
use ReflectionException;
use ReflectionNamedType;
use ReflectionParameter;
use PHPShots\Common\Exceptions\BindingResolutionException;
use PHPShots\Common\Exceptions\CircularDependencyException;

/**
 * Trait Build
 *
 * A trait to handle the instantiation of class dependencies through reflection and parameter overrides,
 * ensuring flexible dependency injection for instantiable classes.
 * 
 * @version 0.1.1
 */
trait Build
{
    /**
     * Stack of parameter overrides for dependency resolution.
     *
     * @var array[]
     */
    protected $with = [];

    /**
     * Determine if a given dependency has a parameter override.
     *
     * @param  ReflectionParameter  $dependency
     * @return bool
     */
    protected function hasParameterOverride(ReflectionParameter $dependency): bool
    {
        return array_key_exists($dependency->name, $this->getLastParameterOverride());
    }

    /**
     * Get a parameter override for a dependency.
     *
     * @param  ReflectionParameter  $dependency
     * @return mixed
     */
    protected function getParameterOverride(ReflectionParameter $dependency): mixed
    {
        return $this->getLastParameterOverride()[$dependency->name];
    }

    /**
     * Get the last parameter override.
     *
     * @return array
     */
    protected function getLastParameterOverride(): array
    {
        return count($this->with) ? end($this->with) : [];
    }

    /**
     * Get the class name for a callable callback, if determinable.
     *
     * @param  callable|string  $callback
     * @return string|false
     */
    protected function getClassForCallable(callable|string $callback): string|false
    {
        if (is_callable($callback) && !($reflector = new ReflectionFunction($callback(...)))->isAnonymous()) {
            return $reflector->getClosureScopeClass()->name ?? false;
        }
        return false;
    }

    /**
     * Determine if a given concrete is buildable.
     *
     * @param  mixed   $concrete
     * @param  string  $abstract
     * @return bool
     */
    protected function isBuildable(mixed $concrete, string $abstract): bool
    {
        return $concrete === $abstract || $concrete instanceof Closure;
    }

    /**
     * Instantiate a concrete instance of a given type.
     *
     * @param  Closure|string  $concrete
     * @return mixed
     * @throws BindingResolutionException|CircularDependencyException
     */
    public function build(Closure|string $concrete): mixed
    {
        if ($concrete instanceof Closure) {
            return $concrete($this, $this->getLastParameterOverride());
        }

        try {
            $reflector = new ReflectionClass($concrete);
        } catch (ReflectionException $e) {
            throw new BindingResolutionException("Target class [$concrete] does not exist.", 0, $e);
        }

        if (!$reflector->isInstantiable()) {
            return $this->notInstantiable($concrete);
        }

        $this->buildStack[] = $concrete;
        $constructor = $reflector->getConstructor();

        if (is_null($constructor)) {
            array_pop($this->buildStack);
            return new $concrete;
        }

        try {
            $instances = $this->resolveDependencies($constructor->getParameters());
        } catch (BindingResolutionException $e) {
            array_pop($this->buildStack);
            throw $e;
        }

        array_pop($this->buildStack);
        return $reflector->newInstanceArgs($instances);
    }

    /**
     * Resolve all dependencies for a given array of ReflectionParameters.
     *
     * @param  ReflectionParameter[]  $dependencies
     * @return array
     * @throws BindingResolutionException
     */
    protected function resolveDependencies(array $dependencies): array
    {
        $results = [];

        foreach ($dependencies as $dependency) {
            if ($this->hasParameterOverride($dependency)) {
                $results[] = $this->getParameterOverride($dependency);
                continue;
            }

            $result = is_null(static::getParameterClassName($dependency))
                ? $this->resolvePrimitive($dependency)
                : $this->resolveClass($dependency);

            $results = $dependency->isVariadic() ? array_merge($results, $result) : array_merge($results, [$result]);
        }

        return $results;
    }

    /**
     * Resolve a primitive dependency.
     *
     * @param  ReflectionParameter  $parameter
     * @return mixed
     * @throws BindingResolutionException
     */
    protected function resolvePrimitive(ReflectionParameter $parameter): mixed
    {
        if (!is_null($concrete = $this->getContextualConcrete('$' . $parameter->getName()))) {
            return $concrete instanceof Closure ? $concrete($this) : $concrete;
        }

        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        return $parameter->isVariadic() ? [] : $this->unresolvablePrimitive($parameter);
    }

    /**
     * Resolve a class-based dependency.
     *
     * @param  ReflectionParameter  $parameter
     * @return mixed
     * @throws BindingResolutionException
     */
    protected function resolveClass(ReflectionParameter $parameter): mixed
    {
        try {
            return $parameter->isVariadic() ? $this->resolveVariadicClass($parameter) : $this->make(static::getParameterClassName($parameter));
        } catch (BindingResolutionException $e) {
            if ($parameter->isDefaultValueAvailable()) {
                array_pop($this->with);
                return $parameter->getDefaultValue();
            }

            throw $e;
        }
    }

    /**
     * Resolve a variadic class-based dependency.
     *
     * @param  ReflectionParameter  $parameter
     * @return mixed
     */
    protected function resolveVariadicClass(ReflectionParameter $parameter): mixed
    {
        $className = static::getParameterClassName($parameter);
        return is_array($concrete = $this->getContextualConcrete($this->getAlias($className)))
            ? array_map(fn($abstract) => $this->resolve($abstract), $concrete)
            : $this->make($className);
    }

    /**
     * Throw an exception for a non-instantiable concrete.
     *
     * @param  string  $concrete
     * @return void
     * @throws BindingResolutionException
     */
    protected function notInstantiable(string $concrete): void
    {
        $message = !empty($this->buildStack)
            ? "Target [$concrete] is not instantiable while building [" . implode(', ', $this->buildStack) . "]."
            : "Target [$concrete] is not instantiable.";

        throw new BindingResolutionException($message);
    }

    /**
     * Retrieve the class name of the parameter type if available.
     *
     * @param  ReflectionParameter  $parameter
     * @return string|null
     */
    protected static function getParameterClassName(ReflectionParameter $parameter): ?string
    {
        $type = $parameter->getType();
        if (!$type instanceof ReflectionNamedType || $type->isBuiltin()) return null;

        $name = $type->getName();
        return match ($name) {
            'self' => $parameter->getDeclaringClass()?->getName(),
            'parent' => $parameter->getDeclaringClass()?->getParentClass()?->getName(),
            default => $name
        };
    }

    /**
     * Throw an exception for an unresolvable primitive dependency.
     *
     * @param  ReflectionParameter  $parameter
     * @return void
     * @throws BindingResolutionException
     */
    protected function unresolvablePrimitive(ReflectionParameter $parameter): void
    {
        throw new BindingResolutionException("Unresolvable dependency resolving [$parameter] in class {$parameter->getDeclaringClass()->getName()}.");
    }
}
