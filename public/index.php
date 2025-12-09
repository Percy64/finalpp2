<?php
/**
 * Main Application Entry Point
 */

// Start session
session_start();

// Load configuration
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

// Load core classes
require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/core/Router.php';

// Initialize router
$router = new Router();

// Define routes

// Home
$router->get('/', 'HomeController', 'index');
$router->get('/home', 'HomeController', 'index');

// User routes
$router->get('/login', 'UserController', 'login');
$router->post('/login', 'UserController', 'login');
$router->get('/registro', 'UserController', 'register');
$router->post('/registro', 'UserController', 'register');
$router->get('/perfil', 'UserController', 'profile');
$router->get('/usuario/{id}', 'UserController', 'profile');
$router->get('/editar-perfil', 'UserController', 'editProfile');
$router->post('/editar-perfil', 'UserController', 'editProfile');
$router->get('/logout', 'UserController', 'logout');
$router->get('/recuperar-password', 'UserController', 'recoverPassword');
$router->post('/recuperar-password', 'UserController', 'recoverPassword');
$router->get('/reset-password', 'UserController', 'resetPassword');
$router->post('/reset-password', 'UserController', 'resetPassword');

// Pet routes
$router->get('/registrar-mascota', 'PetController', 'register');
$router->post('/registrar-mascota', 'PetController', 'register');
$router->get('/mascota/{id}', 'PetController', 'profile');
$router->get('/mascota/{id}/editar', 'PetController', 'edit');
$router->post('/mascota/{id}/editar', 'PetController', 'edit');
$router->get('/mascota/{id}/eliminar', 'PetController', 'delete');
$router->post('/mascota/{id}/eliminar', 'PetController', 'delete');
$router->post('/mascota/{id}/cambiar-estado', 'PetController', 'changeStatus');
$router->get('/mapa', 'PetController', 'map');
$router->get('/qr/{id}', 'PetController', 'qrInfo');

// Legal routes
$router->get('/terminos', 'LegalController', 'terminos');
$router->get('/mision', 'LegalController', 'mision');

// Dispatch the request
$router->dispatch();
