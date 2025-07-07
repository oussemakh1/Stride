<?php

namespace Framework\Security;

class Csrf
{
    protected static $tokenName = '_token';

    public static function generateToken(): string
    {
        if (empty($_SESSION[self::$tokenName])) {
            $_SESSION[self::$tokenName] = bin2hex(random_bytes(32));
        }
        return $_SESSION[self::$tokenName];
    }

    public static function getToken(): string
    {
        return $_SESSION[self::$tokenName] ?? '';
    }

    public static function validateToken(string $token): bool
    {
        return isset($_SESSION[self::$tokenName]) && hash_equals($_SESSION[self::$tokenName], $token);
    }

    public static function getTokenName(): string
    {
        return self::$tokenName;
    }
}
