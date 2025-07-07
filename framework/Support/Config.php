<?php

namespace Framework\Support;

class Config
{
    protected static array $config = [];

    public static function load(array $config): void
    {
        static::$config = $config;
    }

    public static function get(string $key, $default = null)
    {
        $parts = explode('.', $key);
        $value = static::$config;

        foreach ($parts as $part) {
            if (isset($value[$part])) {
                $value = $value[$part];
            } else {
                return $default;
            }
        }

        return $value;
    }
}