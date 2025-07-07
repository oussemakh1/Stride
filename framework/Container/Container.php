<?php

namespace Framework\Container;

use ReflectionClass;
use ReflectionParameter;

class Container
{
    protected $bindings = [];

    public function bind(string $abstract, $concrete = null, bool $shared = false): void
    {
        if (is_null($concrete)) {
            $concrete = $abstract;
        }
        $this->bindings[$abstract] = compact('concrete', 'shared');
    }

    public function singleton(string $abstract, $concrete = null): void
    {
        $this->bind($abstract, $concrete, true);
    }

    public function make(string $abstract, array $parameters = []): object
    {
        return $this->resolve($abstract, $parameters);
    }

    protected function resolve(string $abstract, array $parameters = []): object
    {
        if (isset($this->bindings[$abstract])) {
            $concrete = $this->bindings[$abstract]['concrete'];
            $shared = $this->bindings[$abstract]['shared'];

            if ($shared && isset($this->bindings[$abstract]['instance'])) {
                return $this->bindings[$abstract]['instance'];
            }

            $object = $this->build($concrete, $parameters);

            if ($shared) {
                $this->bindings[$abstract]['instance'] = $object;
            }

            return $object;
        }

        return $this->build($abstract, $parameters);
    }

    protected function build($concrete, array $parameters = []): object
    {
        if ($concrete instanceof \Closure) {
            return $concrete($this, $parameters);
        }

        $reflector = new ReflectionClass($concrete);

        if (!$reflector->isInstantiable()) {
            throw new \Exception("Class {$concrete} is not instantiable.");
        }

        $constructor = $reflector->getConstructor();

        if (is_null($constructor)) {
            return new $concrete();
        }

        $dependencies = $this->getDependencies($constructor->getParameters(), $parameters);

        return $reflector->newInstanceArgs($dependencies);
    }

    protected function getDependencies(array $parameters, array $primitives = []): array
    {
        $dependencies = [];
        foreach ($parameters as $parameter) {
            $dependency = $parameter->getType() && !$parameter->getType()->isBuiltin()
                ? $this->resolve($parameter->getType()->getName())
                : $this->resolvePrimitive($parameter, $primitives);

            $dependencies[] = $dependency;
        }

        return $dependencies;
    }

    protected function resolvePrimitive(ReflectionParameter $parameter, array $primitives = []): mixed
    {
        if (array_key_exists($parameter->name, $primitives)) {
            return $primitives[$parameter->name];
        }

        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        throw new \Exception("Unresolvable dependency: {$parameter->name}");
    }
}