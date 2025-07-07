<?php

namespace Framework\Cache;

interface CacheInterface
{
    public function get(string $key, $default = null);

    public function set(string $key, $value, int $ttl = null);

    public function delete(string $key): bool;

    public function has(string $key): bool;
}
