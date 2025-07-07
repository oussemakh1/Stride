<?php

namespace Framework\Cache;

class FileStore implements CacheInterface
{
    protected $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function get(string $key, $default = null)
    {
        $file = $this->path . DIRECTORY_SEPARATOR . $key;

        if (!file_exists($file)) {
            return $default;
        }

        $contents = unserialize(file_get_contents($file));

        if (time() > $contents['expires']) {
            $this->delete($key);
            return $default;
        }

        return $contents['value'];
    }

    public function set(string $key, $value, int $ttl = null): bool
    {
        $file = $this->path . DIRECTORY_SEPARATOR . $key;
        $ttl = $ttl ?? config('cache.ttl', 3600);

        $contents = [
            'value' => $value,
            'expires' => time() + $ttl,
        ];

        return file_put_contents($file, serialize($contents)) !== false;
    }

    public function delete(string $key): bool
    {
        $file = $this->path . DIRECTORY_SEPARATOR . $key;

        if (file_exists($file)) {
            return unlink($file);
        }

        return false;
    }

    public function has(string $key): bool
    {
        $file = $this->path . DIRECTORY_SEPARATOR . $key;
        return file_exists($file);
    }
}
