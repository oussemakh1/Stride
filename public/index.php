<?php

declare(strict_types=1);

// Session management is now handled by Framework\Support\Session

require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

use Framework\Cache\CacheManager;
use Framework\Http\RouteHandler;
use Framework\Support\Facades\Cache;
use Framework\Support\Config;
use Framework\Support\Lang;
use Framework\Support\Session;
use Framework\Template\TemplateEngine;
use Framework\Support\ErrorHandler;
use Framework\Support\Asset;
use Framework\Events\EventDispatcher;
use Framework\Core\Model;
use Framework\Container\Container;
use Framework\Queue\Queue;
use Framework\Security\Csrf;

// Start the session
Session::start();

// Load application configuration
$appConfig = require __DIR__ . '/../config/app.php';
$cacheConfig = require __DIR__ . '/../config/cache.php';
$databaseConfig = require __DIR__ . '/../config/database.php';

// Load application configuration into the Config class
Config::load(array_merge($appConfig, ['cache' => $cacheConfig, 'database' => $databaseConfig, 'sidebar' => require __DIR__ . '/../config/sidebar.php']));

// Register error handler
$errorHandler = new ErrorHandler($appConfig['debug']);
$errorHandler->register();

// Initialize localization
Lang::setLocale($appConfig['locale']);
Lang::setFallbackLocale($appConfig['fallback_locale']);

// Initialize Container
$container = new Container();

// Bind core services to the container
$container->singleton(TemplateEngine::class, function () use ($appConfig) {
    $engine = new TemplateEngine(
        __DIR__ . '/../app/Views/',
        __DIR__ . '/../cache/'
    );
    if ($appConfig['cache_enabled'] ?? false) {
        $engine->enableCache($appConfig['cache_lifetime'] ?? 3600);
    }
    // Register custom functions and filters
    $engine->registerFunction('greet', function ($name) {
        return "Hello, " . htmlspecialchars($name) . "!";
    });
    $engine->registerFilter('uppercase', function ($value) {
        return strtoupper($value);
    });
    $engine->registerFunction('asset', function ($path) {
        return Asset::url($path);
    });
    return $engine;
});

$container->singleton(EventDispatcher::class, function () {
    $dispatcher = new EventDispatcher();
    // Register AppelObserver

    return $dispatcher;
});

$container->singleton(Queue::class, function () {
    return new Queue(__DIR__ . '/../queue_jobs');
});

$container->singleton(CacheManager::class, function () {
    return new CacheManager();
});

// Set the event dispatcher on the Model class
Model::setEventDispatcher($container->make(EventDispatcher::class));

// Set the cache manager on the Cache facade
Cache::setManager($container->make(CacheManager::class));

// Generate CSRF token for the current session
Csrf::generateToken();

// Resolve RouteHandler from the container
$router = $container->make(RouteHandler::class);

// Pass the container to the router for controller dependency resolution
$router->setContainer($container);

// The routes are now included here
require_once __DIR__ . '/../routes/web.php';
require_once __DIR__ . '/../routes/api.php';

$router->handleRequest();
