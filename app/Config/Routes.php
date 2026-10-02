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

// Media is content: ADMIN and OPERATOR may manage it.
$routes->get('manager/media', 'Manager\MediaController::index', ['filter' => 'auth']);
$routes->post('manager/media/upload', 'Manager\MediaController::upload', ['filter' => 'auth']);
$routes->post('manager/media/(:num)/update', 'Manager\MediaController::update/$1', ['filter' => 'auth']);
$routes->post('manager/media/(:num)/delete', 'Manager\MediaController::delete/$1', ['filter' => 'auth']);

// Global settings and feature visibility are ADMIN-only.
$routes->get('manager/settings', 'Manager\SettingsController::index', ['filter' => 'admin']);
$routes->post('manager/settings', 'Manager\SettingsController::update', ['filter' => 'admin']);
$routes->get('manager/features', 'Manager\FeatureController::index', ['filter' => 'admin']);
$routes->post('manager/features', 'Manager\FeatureController::update', ['filter' => 'admin']);
