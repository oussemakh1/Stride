<?php

namespace Framework\Auth;

use App\Models\Commercial;
use Framework\Support\Auth;

class AuthManager
{
    // For simplicity, roles and permissions are hardcoded. In a real app, these would come from a database.
    protected $roles = [
        'admin' => [
            'manage_appels',
            'manage_users',
            'view_reports',
        ],
        'commercial' => [
            'manage_appels',
        ],
    ];

    public function hasRole(string $role): bool
    {
        $user = Auth::user();
        if (!$user || !isset($user->role)) {
            return false;
        }
        return $user->role === $role;
    }

    public function hasPermission(string $permission): bool
    {
        $user = Auth::user();
        if (!$user || !isset($user->role)) {
            return false;
        }

        $userRole = $user->role;

        if (isset($this->roles[$userRole]) && in_array($permission, $this->roles[$userRole])) {
            return true;
        }

        return false;
    }
}
