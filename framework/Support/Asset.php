<?php

namespace Framework\Support;

class Asset
{
    protected static $version = null;

    public static function url(string $path): string
    {
        // Ensure path starts without a leading slash if it's already in public/
        $path = ltrim($path, '/');

        // Append a version string for cache busting
        if (self::$version === null) {
            // Use filemtime of index.php as a simple version string
            self::$version = filemtime(__DIR__ . '/../../public/index.php');
        }

        return '/' . $path . '?v=' . self::$version;
    }
}
