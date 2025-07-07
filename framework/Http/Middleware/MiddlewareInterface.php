<?php

namespace Framework\Http\Middleware;

use Framework\Http\Request;

interface MiddlewareInterface
{
    public function handle(Request $request, callable $next);
}
