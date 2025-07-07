<?php

use App\Controllers\LandingPageController;
use App\Middleware\AuthMiddleware;

/** @var Framework\Http\RouteHandler $router */

// Define your routes here
$router->get('/', [LandingPageController::class, 'index'])->name('landingpage');
$router->get('/docs', [LandingPageController::class, 'documentation'])->name('landingpage');

