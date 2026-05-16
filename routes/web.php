<?php

/** @var \App\Core\Router $router */

$router->get('', 'PagesController@home');
$router->get('article', 'ArticleController@show');
$router->get('category', 'CategoryController@show');
$router->get('search', 'SearchController@index');

// Auth routes
$router->get('login', 'AuthController@showLoginForm');
$router->post('login', 'AuthController@login');
$router->get('register', 'AuthController@showRegistrationForm');
$router->post('register', 'AuthController@register');
$router->get('logout', 'AuthController@logout');

$router->get('profile', 'UserController@profile');
