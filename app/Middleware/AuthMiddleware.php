<?php

namespace App\Middleware;

use Framework\Http\Middleware\MiddlewareInterface;
use Framework\Http\Request;
use Framework\Support\Auth;

class AuthMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next)
    {
        if (!Auth::check()) {
            // Redirect to login or return an error
            if (getenv('APP_ENV') === 'testing') {
                throw new \Exception('Redirect to /login');
            }
            header('Location: /login'); // Assuming a login route exists
            exit();
        }
        return $next($request);
    }
}
