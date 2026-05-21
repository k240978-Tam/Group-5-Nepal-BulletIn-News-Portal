<?php

/** @var \App\Core\Router $router */

$router->get('', 'PagesController@home');
$router->get('article', 'ArticleController@show');
$router->get('article/summarize', 'ArticleController@summarize');
$router->get('article/{id}', 'ArticleController@show');
$router->get('category', 'ArticleController@category');
$router->get('category/{id}', 'ArticleController@category');
$router->get('search', 'ArticleController@search');
$router->post('chat', 'ChatController@sendMessage');

// Auth routes
$router->get('login', 'AuthController@showLoginForm');
$router->post('login', 'AuthController@login');
$router->get('register', 'AuthController@showRegistrationForm');
$router->post('register', 'AuthController@register');
$router->get('logout', 'AuthController@logout');

$router->get('profile', 'UserController@profile');
