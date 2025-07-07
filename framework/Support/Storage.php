<?php

namespace Framework\Support;

class Storage
{
    protected static $disk = 'local';

    public static function disk(string $disk): self
    {
        self::$disk = $disk;
        return new self();
    }

    public static function path(string $path = ''): string
    {
        $storagePath = config('app.storage_path', __DIR__ . '/../../storage');
        return $storagePath . DIRECTORY_SEPARATOR . $path;
    }

    public static function put(string $path, $contents): bool
    {
        $fullPath = self::path($path);
        $directory = dirname($fullPath);

        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        return file_put_contents($fullPath, $contents) !== false;
    }

    public static function get(string $path): string|false
    {
        $fullPath = self::path($path);

        if (!self::exists($path)) {
            return false;
        }

        return file_get_contents($fullPath);
    }

    public static function exists(string $path): bool
    {
        return file_exists(self::path($path));
    }

    public static function delete(string $path): bool
    {
        $fullPath = self::path($path);

        if (!self::exists($path)) {
            return false;
        }

        return unlink($fullPath);
    }

    public static function url(string $path): string
    {
        $baseUrl = config('app.url', '');
        return $baseUrl . '/storage/' . $path;
    }
}
