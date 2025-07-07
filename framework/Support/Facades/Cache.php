<?php

namespace Framework\Support\Facades;

use Framework\Cache\CacheManager;

class Cache
{
    protected static $manager;

    public static function setManager(CacheManager $manager): void
    {
        self::$manager = $manager;
    }

    public static function __callStatic($method, $arguments)
    {
        return self::$manager->{$method}(...$arguments);
    }
}
