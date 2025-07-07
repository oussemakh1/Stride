<?php

/** @var Framework\Http\RouteHandler $router */

// API routes
$router->get('/api/products',['ProductController@index']);

