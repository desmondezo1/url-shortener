<?php

/** @var \Laravel\Lumen\Routing\Router $router */


$router->get('/', function () {
    return view('home');
});

// URL Shortener API Routes
$router->post('/encode', 'UrlController@encode');
$router->post('/decode', 'UrlController@decode');
$router->get('/api-docs', function () {
    return view('swagger');
});

$router->get('/{code}', 'UrlController@redirect');
