<?php
require_once __DIR__ . '/../config/bootstrap.php';

use App\Core\Router;

$router = new Router();

// Define Routes
$router->get('/', 'HomeController@index');
$router->get('/category', 'HomeController@category');
$router->get('/search', 'HomeController@search');

$router->get('/article', 'ArticleController@show');
$router->post('/article/comment', 'ArticleController@comment');

$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');
$router->get('/register', 'AuthController@showRegister');
$router->post('/register', 'AuthController@register');
$router->get('/logout', 'AuthController@logout');

$router->get('/profile', 'UserController@profile');
$router->post('/profile/update', 'UserController@update');

$router->get('/bulletin', 'BulletinController@index');

// API Routes
$router->post('/api/chat', 'ApiController@chat');
$router->post('/api/summarize', 'ApiController@summarize');
$router->post('/api/bulletin', 'ApiController@bulletin');

$router->get('/admin', 'AdminController@dashboard');
$router->get('/admin/editor', 'AdminController@editor');
$router->post('/admin/editor/save', 'AdminController@saveArticle');
$router->post('/admin/article/delete', 'AdminController@deleteArticle');
$router->post('/admin/article/process', 'AdminController@processArticle');
$router->get('/admin/users', 'AdminController@users');
$router->post('/admin/users/create', 'AdminController@createUser');
$router->post('/admin/users/role', 'AdminController@updateUserRole');
$router->post('/admin/users/delete', 'AdminController@deleteUser');
$router->get('/admin/media', 'AdminController@media');
$router->get('/admin/review', 'AdminController@review');
$router->get('/admin/logs', 'AdminController@logs');
$router->get('/admin/settings', 'AdminController@settings');

// Determine URI without the base path and entry point script
$basePath = '/newsportal';
$uri = $_SERVER['REQUEST_URI'];

// Strip base path if present
if (strpos($uri, $basePath) === 0) {
    $uri = substr($uri, strlen($basePath));
}

$scriptName = $_SERVER['SCRIPT_NAME'];
$scriptDir = dirname($scriptName);

// Adjust script name and dir to remove the base path prefix
if (strpos($scriptName, $basePath) === 0) {
    $scriptName = substr($scriptName, strlen($basePath));
}
if (strpos($scriptDir, $basePath) === 0) {
    $scriptDir = substr($scriptDir, strlen($basePath));
}

// Strip script name (e.g. /public/index.php) or directory (e.g. /public)
if (strpos($uri, $scriptName) === 0) {
    $uri = substr($uri, strlen($scriptName));
} elseif ($scriptDir !== '/' && $scriptDir !== '\\' && !empty($scriptDir) && strpos($uri, $scriptDir) === 0) {
    $uri = substr($uri, strlen($scriptDir));
}

// Remove query string for routing match
$uriPath = explode('?', $uri)[0];

// Remove trailing .php extension to make legacy links match clean routes
$uriPath = preg_replace('/\.php$/', '', $uriPath);

$router->dispatch($uriPath, $_SERVER['REQUEST_METHOD']);
