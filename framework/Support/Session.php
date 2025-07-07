<?php

namespace Framework\Support;

class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            // Set secure cookie parameters before session_start()
            // This helps prevent XSS attacks by making cookies inaccessible to client-side scripts
            ini_set('session.cookie_httponly', '1');
            // This ensures cookies are only sent over HTTPS connections
            // Only enable in production if you are using HTTPS
            // ini_set('session.cookie_secure', '1'); 
            session_start();
        }
    }

    public static function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function flash(string $key, string $message): void
    {
        self::set('flash_' . $key, $message);
    }

    public static function getFlash(string $key)
    {
        $message = self::get('flash_' . $key);
        self::remove('flash_' . $key);
        return $message;
    }

    public static function hasFlash(string $key): bool
    {
        return self::has('flash_' . $key);
    }
}