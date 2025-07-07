<?php

namespace Framework\Http;

use ReflectionMethod;
use ReflectionParameter;
use Framework\Http\Middleware\MiddlewareInterface;
use Framework\Container\Container;
use Framework\Security\Csrf;

class RouteHandler
{
    private $routes = [];
    private $namedRoutes = [];
    private $lastRouteUrl = null;
    private $lastRouteMethod = null;
    private $currentPrefix = '';
    private $routeMiddleware = [];
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function setContainer(Container $container): void
    {
        $this->container = $container;
    }

    public function get(string $url, array $handler): self
    {
        return $this->setRoute('GET', $url, $handler);
    }

    public function post(string $url, array $handler): self
    {
        return $this->setRoute('POST', $url, $handler);
    }

    public function put(string $url, array $handler): self
    {
        return $this->setRoute('PUT', $url, $handler);
    }

    public function delete(string $url, array $handler): self
    {
        return $this->setRoute('DELETE', $url, $handler);
    }

    private function setRoute(string $method, string $url, array $handler): self
    {
        $fullUrl = $this->currentPrefix . $url;
        $this->routes[$method][$fullUrl] = $handler;
        $this->lastRouteUrl = $fullUrl;
        $this->lastRouteMethod = $method;
        $this->routeMiddleware[$method][$fullUrl] = [];
        return $this;
    }

    public function name(string $name): self
    {
        if ($this->lastRouteUrl === null || $this->lastRouteMethod === null) {
            throw new \Exception("Cannot name a route before defining it.");
        }
        $this->namedRoutes[$name] = [
            'method' => $this->lastRouteMethod,
            'url' => $this->lastRouteUrl
        ];
        
        return $this;
    }

    public function middleware(array $middlewareClasses): self
    {
        if ($this->lastRouteUrl === null || $this->lastRouteMethod === null) {
            throw new \Exception("Cannot assign middleware to a route before defining it.");
        }
        foreach ($middlewareClasses as $middleware) {
            if (is_array($middleware)) {
                $middlewareClass = key($middleware);
                $params = current($middleware);
                $this->routeMiddleware[$this->lastRouteMethod][$this->lastRouteUrl][] = [$middlewareClass, $params];
            } else {
                $this->routeMiddleware[$this->lastRouteMethod][$this->lastRouteUrl][] = $middleware;
            }
        }
        return $this;
    }

    public function group(array $options, callable $callback): void
    {
        $oldPrefix = $this->currentPrefix;
        $this->currentPrefix .= $options['prefix'] ?? '';

        $callback($this);

        $this->currentPrefix = $oldPrefix;
    }

    public function handleRequest(): void
    {
        $requestUri = $_SERVER['REQUEST_URI'];
        $uri = parse_url($requestUri, PHP_URL_PATH);
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        // CSRF Protection for non-GET requests
        if ($requestMethod !== 'GET') {
            $token = $_POST[Csrf::getTokenName()] ?? '';
            if (!Csrf::validateToken($token)) {
                header("HTTP/1.0 419 Page Expired");
                echo '419 Page Expired - CSRF token mismatch.';
                exit();
            }
        }

        if (!isset($this->routes[$requestMethod])) {
            header("HTTP/1.0 405 Method Not Allowed");
            echo '405 Method Not Allowed';
            return;
        }

        foreach ($this->routes[$requestMethod] as $routeUrl => $handler) {
            $pattern = preg_replace('#/{([a-zA-Z0-9_]+)}#', '/([^/]+)', $routeUrl);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);

                $controllerName = $handler[0];
                $methodName = $handler[1];

                $controller = $this->container->make($controllerName);

                $reflectionMethod = new ReflectionMethod($controller, $methodName);
                $methodParameters = $reflectionMethod->getParameters();

                $args = [];
                $paramIndex = 0;

                foreach ($methodParameters as $parameter) {
                    if ($parameter->getType() && !$parameter->getType()->isBuiltin()) {
                        $args[] = $this->container->make($parameter->getType()->getName());
                    } elseif (isset($matches[$paramIndex])) {
                        if ($parameter->getType() && $parameter->getType()->getName() === 'int') {
                            $args[] = (int) $matches[$paramIndex];
                        } else {
                            $args[] = $matches[$paramIndex];
                        }
                        $paramIndex++;
                    } else {
                        if ($parameter->isOptional()) {
                            $args[] = $parameter->getDefaultValue();
                        } else {
                            throw new \Exception("Missing required parameter {$parameter->getName()} for route {$routeUrl}");
                        }
                    }
                }

                $middlewareStack = $this->routeMiddleware[$requestMethod][$routeUrl] ?? [];
                $request = $this->container->make(Request::class);

                $next = function (Request $request) use ($controller, $methodName, $args) {
                    return call_user_func_array([$controller, $methodName], $args);
                };

                $pipeline = array_reduce(
                    array_reverse($middlewareStack),
                    function ($next, $middleware) use ($request) {
                        return function (Request $request) use ($next, $middleware) {
                            if (is_array($middleware)) {
                                $middlewareClass = $middleware[0];
                                $params = $middleware[1];
                                $middlewareInstance = $this->container->make($middlewareClass, $params);
                            } else {
                                $middlewareClass = $middleware;
                                $middlewareInstance = $this->container->make($middlewareClass);
                            }

                            if (!$middlewareInstance instanceof MiddlewareInterface) {
                                throw new \Exception("Class {$middlewareClass} must implement MiddlewareInterface.");
                            }
                            return $middlewareInstance->handle($request, $next);
                        };
                    },
                    $next
                );

                echo $pipeline($request);

                return;
            }
        }

        header("HTTP/1.0 404 Not Found");
        echo '404 Not Found';
    }

    public static function route(string $name, array $params = []): string
    {
        global $router;

        if (!isset($router->namedRoutes[$name])) {
            throw new \Exception("Route [{$name}] not found.");
        }

        $route = $router->namedRoutes[$name];
        $url = $route['url'];

        foreach ($params as $key => $value) {
            $url = str_replace("{{$key}}", (string) $value, $url);
        }

        $url = preg_replace('#/{[a-zA-Z0-9_]+}#', '', $url);

        return $url;
    }
}