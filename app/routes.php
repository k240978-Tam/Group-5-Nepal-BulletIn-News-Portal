<?php
use App\Controllers\HomeController;
use App\Controllers\ArticleController;
use App\Controllers\AuthController;

/** @var \App\Core\Router $router */

// Frontend Routes
$router->get('/', [HomeController::class, 'index']);
$router->get('/article/{id}', [ArticleController::class, 'show']);
$router->get('/category/{id}', [ArticleController::class, 'category']);
$router->get('/search', [ArticleController::class, 'search']);

// Auth Routes
$router->get('/login', [AuthController::class, 'loginForm']);
$router->post('/login', [AuthController::class, 'processLogin']);
$router->get('/register', [AuthController::class, 'registerForm']);
$router->post('/register', [AuthController::class, 'processRegister']);
$router->get('/logout', [AuthController::class, 'logout']);

// Admin routes will be added here later...
