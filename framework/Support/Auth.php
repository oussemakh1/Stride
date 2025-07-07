<?php

namespace Framework\Support;

use Framework\Auth\AuthManager;
use Framework\Support\Config;

class Auth
{
    public static function attempt(string $email, string $password): bool
    {
        $userModelClass = Config::get('app.user_model');
        $userModel = new $userModelClass();
        $user = $userModel->findBy('email', $email);

        if ($user && password_verify($password, $user->password)) {
            $_SESSION['user'] = $user;
            // For demonstration, assign a role. In a real app, this would come from the user data.
            $_SESSION['user']->role = 'commercial'; 
            return true;
        }

        return false;
    }

    public static function check(): bool
    {
        return isset($_SESSION['user']);
    }

    public static function user(): ?object
    {
        return $_SESSION['user'] ?? null;
    }

    public static function logout(): void
    {
        session_unset();
        session_destroy();
    }

    public static function hasRole(string $role): bool
    {
        $authManager = new AuthManager(); // In a real app, resolve from container
        return $authManager->hasRole($role);
    }

    public static function hasPermission(string $permission): bool
    {
        $authManager = new AuthManager(); // In a real app, resolve from container
        return $authManager->hasPermission($permission);
    }
}