<?php

namespace Framework\Cache;

use InvalidArgumentException;

class CacheManager
{
    protected $stores = [];

    public function __construct()
    {
        //
    }

    public function store(string $name = null)
    {
        $name = $name ?? $this->getDefaultDriver();

        if (!isset($this->stores[$name])) {
            $this->stores[$name] = $this->resolve($name);
        }

        return $this->stores[$name];
    }

    protected function resolve(string $name)
    {
        $config = $this->getConfig($name);

        $driverMethod = 'create' . ucfirst($config['driver']) . 'Driver';

        if (method_exists($this, $driverMethod)) {
            return $this->{$driverMethod}($config);
        }

        throw new InvalidArgumentException("Driver [{$config['driver']}] is not supported.");
    }

    protected function createFileDriver(array $config): FileStore
    {
        return new FileStore($config['path']);
    }

    protected function getConfig(string $name): array
    {
        return config("cache.stores.{$name}");
    }

    public function getDefaultDriver(): string
    {
        return config('cache.default');
    }

    public function __call($method, $parameters)
    {
        return $this->store()->{$method}(...$parameters);
    }
}
