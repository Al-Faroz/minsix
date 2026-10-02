<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

$routes->get('manager/login', 'Manager\AuthController::login');
$routes->post('manager/login', 'Manager\AuthController::attempt');

$routes->get('manager', 'Manager\DashboardController::index', ['filter' => 'auth']);
$routes->post('manager/logout', 'Manager\AuthController::logout', ['filter' => 'auth']);

$routes->get('manager/account/password', 'Manager\AccountController::password', ['filter' => 'auth']);
$routes->post('manager/account/password', 'Manager\AccountController::updatePassword', ['filter' => 'auth']);
