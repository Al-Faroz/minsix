<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');
$routes->get('profil', 'PageController::profile');
$routes->get('program', 'PageController::program');
$routes->get('gtk', 'PageController::gtk');
$routes->get('kabar', 'KabarController::index');
$routes->get('kabar/berita/(:segment)', 'KabarController::newsDetail/$1');
$routes->get('kabar/prestasi/(:segment)', 'KabarController::achievementDetail/$1');
$routes->get('kabar/agenda/(:segment)', 'KabarController::eventDetail/$1');
$routes->get('kabar/galeri/(:segment)', 'KabarController::galleryDetail/$1');
$routes->get('spmb', 'PageController::spmb');
$routes->get('sitemap.xml', 'SitemapController::index');

$routes->get('manager/login', 'Manager\AuthController::login');
$routes->post('manager/login', 'Manager\AuthController::attempt');

$routes->get('manager', 'Manager\DashboardController::index', ['filter' => 'auth']);
$routes->post('manager/logout', 'Manager\AuthController::logout', ['filter' => 'auth']);

$routes->get('manager/account/password', 'Manager\AccountController::password', ['filter' => 'auth']);
$routes->post('manager/account/password', 'Manager\AccountController::updatePassword', ['filter' => 'auth']);

// Content modules: ADMIN and OPERATOR.
$routes->get('manager/homepage', 'Manager\\HomepageController::index', ['filter' => 'auth']);
$routes->post('manager/homepage/(:num)', 'Manager\\HomepageController::update/$1', ['filter' => 'auth']);
$routes->get('manager/profile', 'Manager\ProfileController::index', ['filter' => 'auth']);
$routes->post('manager/profile/(:num)', 'Manager\ProfileController::update/$1', ['filter' => 'auth']);

$routes->get('manager/programs', 'Manager\ProgramController::index', ['filter' => 'auth']);
$routes->get('manager/programs/new', 'Manager\ProgramController::new', ['filter' => 'auth']);
$routes->post('manager/programs', 'Manager\ProgramController::create', ['filter' => 'auth']);
$routes->get('manager/programs/(:num)/edit', 'Manager\ProgramController::edit/$1', ['filter' => 'auth']);
$routes->post('manager/programs/(:num)', 'Manager\ProgramController::update/$1', ['filter' => 'auth']);
$routes->post('manager/programs/(:num)/delete', 'Manager\ProgramController::delete/$1', ['filter' => 'auth']);

$routes->get('manager/gtk', 'Manager\GtkController::index', ['filter' => 'auth']);
$routes->get('manager/gtk/new', 'Manager\GtkController::new', ['filter' => 'auth']);
$routes->post('manager/gtk', 'Manager\GtkController::create', ['filter' => 'auth']);
$routes->get('manager/gtk/(:num)/edit', 'Manager\GtkController::edit/$1', ['filter' => 'auth']);
$routes->post('manager/gtk/(:num)', 'Manager\GtkController::update/$1', ['filter' => 'auth']);
$routes->post('manager/gtk/(:num)/delete', 'Manager\GtkController::delete/$1', ['filter' => 'auth']);

$routes->get('manager/gtk-roles', 'Manager\GtkRoleController::index', ['filter' => 'auth']);
$routes->post('manager/gtk-roles', 'Manager\GtkRoleController::create', ['filter' => 'auth']);
$routes->post('manager/gtk-roles/(:num)', 'Manager\GtkRoleController::update/$1', ['filter' => 'auth']);
$routes->post('manager/gtk-roles/(:num)/delete', 'Manager\GtkRoleController::delete/$1', ['filter' => 'auth']);

// Kabar Madrasah.
$routes->get('manager/news', 'Manager\NewsController::index', ['filter' => 'auth']);
$routes->get('manager/news/new', 'Manager\NewsController::new', ['filter' => 'auth']);
$routes->post('manager/news', 'Manager\NewsController::create', ['filter' => 'auth']);
$routes->get('manager/news/(:num)/edit', 'Manager\NewsController::edit/$1', ['filter' => 'auth']);
$routes->post('manager/news/(:num)', 'Manager\NewsController::update/$1', ['filter' => 'auth']);
$routes->post('manager/news/(:num)/delete', 'Manager\NewsController::delete/$1', ['filter' => 'auth']);

$routes->get('manager/events', 'Manager\EventController::index', ['filter' => 'auth']);
$routes->get('manager/events/new', 'Manager\EventController::new', ['filter' => 'auth']);
$routes->post('manager/events', 'Manager\EventController::create', ['filter' => 'auth']);
$routes->get('manager/events/(:num)/edit', 'Manager\EventController::edit/$1', ['filter' => 'auth']);
$routes->post('manager/events/(:num)', 'Manager\EventController::update/$1', ['filter' => 'auth']);
$routes->post('manager/events/(:num)/delete', 'Manager\EventController::delete/$1', ['filter' => 'auth']);

$routes->get('manager/achievements', 'Manager\AchievementController::index', ['filter' => 'auth']);
$routes->get('manager/achievements/new', 'Manager\AchievementController::new', ['filter' => 'auth']);
$routes->post('manager/achievements', 'Manager\AchievementController::create', ['filter' => 'auth']);
$routes->get('manager/achievements/(:num)/edit', 'Manager\AchievementController::edit/$1', ['filter' => 'auth']);
$routes->post('manager/achievements/(:num)', 'Manager\AchievementController::update/$1', ['filter' => 'auth']);
$routes->post('manager/achievements/(:num)/delete', 'Manager\AchievementController::delete/$1', ['filter' => 'auth']);

$routes->get('manager/galleries', 'Manager\GalleryController::index', ['filter' => 'auth']);
$routes->get('manager/galleries/new', 'Manager\GalleryController::new', ['filter' => 'auth']);
$routes->post('manager/galleries', 'Manager\GalleryController::create', ['filter' => 'auth']);
$routes->get('manager/galleries/(:num)/edit', 'Manager\GalleryController::edit/$1', ['filter' => 'auth']);
$routes->post('manager/galleries/(:num)', 'Manager\GalleryController::update/$1', ['filter' => 'auth']);
$routes->post('manager/galleries/(:num)/delete', 'Manager\GalleryController::delete/$1', ['filter' => 'auth']);
$routes->post('manager/galleries/(:num)/items', 'Manager\GalleryController::addItems/$1', ['filter' => 'auth']);
$routes->post('manager/galleries/(:num)/items/(:num)', 'Manager\GalleryController::updateItem/$1/$2', ['filter' => 'auth']);
$routes->post('manager/galleries/(:num)/items/(:num)/delete', 'Manager\GalleryController::deleteItem/$1/$2', ['filter' => 'auth']);

// SPMB.
$routes->get('manager/spmb', 'Manager\SpmbController::index', ['filter' => 'auth']);
$routes->get('manager/spmb/new', 'Manager\SpmbController::new', ['filter' => 'auth']);
$routes->post('manager/spmb', 'Manager\SpmbController::create', ['filter' => 'auth']);
$routes->get('manager/spmb/(:num)/edit', 'Manager\SpmbController::edit/$1', ['filter' => 'auth']);
$routes->post('manager/spmb/(:num)', 'Manager\SpmbController::update/$1', ['filter' => 'auth']);
$routes->post('manager/spmb/(:num)/current', 'Manager\SpmbController::setCurrent/$1', ['filter' => 'auth']);
$routes->post('manager/spmb/(:num)/delete', 'Manager\SpmbController::delete/$1', ['filter' => 'auth']);

$routes->post('manager/spmb/(:num)/requirements', 'Manager\SpmbController::addRequirement/$1', ['filter' => 'auth']);
$routes->post('manager/spmb/(:num)/requirements/(:num)', 'Manager\SpmbController::updateRequirement/$1/$2', ['filter' => 'auth']);
$routes->post('manager/spmb/(:num)/requirements/(:num)/delete', 'Manager\SpmbController::deleteRequirement/$1/$2', ['filter' => 'auth']);

$routes->post('manager/spmb/(:num)/faq', 'Manager\SpmbController::addFaq/$1', ['filter' => 'auth']);
$routes->post('manager/spmb/(:num)/faq/(:num)', 'Manager\SpmbController::updateFaq/$1/$2', ['filter' => 'auth']);
$routes->post('manager/spmb/(:num)/faq/(:num)/delete', 'Manager\SpmbController::deleteFaq/$1/$2', ['filter' => 'auth']);

// Instagram content: ADMIN and OPERATOR manage manual fallback/cache visibility.
$routes->get('manager/instagram', 'Manager\InstagramContentController::index', ['filter' => 'auth']);
$routes->post('manager/instagram', 'Manager\InstagramContentController::create', ['filter' => 'auth']);
$routes->post('manager/instagram/(:num)', 'Manager\InstagramContentController::update/$1', ['filter' => 'auth']);
$routes->post('manager/instagram/(:num)/delete', 'Manager\InstagramContentController::delete/$1', ['filter' => 'auth']);

// Instagram integration configuration: ADMIN-only.
$routes->get('manager/instagram-settings', 'Manager\InstagramSettingsController::index', ['filter' => 'admin']);
$routes->post('manager/instagram-settings', 'Manager\InstagramSettingsController::update', ['filter' => 'admin']);
$routes->post('manager/instagram-settings/sync', 'Manager\InstagramSettingsController::sync', ['filter' => 'admin']);

// Media is content.
$routes->get('manager/media', 'Manager\MediaController::index', ['filter' => 'auth']);
$routes->post('manager/media/upload', 'Manager\MediaController::upload', ['filter' => 'auth']);
$routes->post('manager/media/(:num)/update', 'Manager\MediaController::update/$1', ['filter' => 'auth']);
$routes->post('manager/media/(:num)/delete', 'Manager\MediaController::delete/$1', ['filter' => 'auth']);

// Global settings and feature visibility are ADMIN-only.
$routes->get('manager/settings', 'Manager\SettingsController::index', ['filter' => 'admin']);
$routes->post('manager/settings', 'Manager\SettingsController::update', ['filter' => 'admin']);
$routes->get('manager/features', 'Manager\FeatureController::index', ['filter' => 'admin']);
$routes->post('manager/features', 'Manager\FeatureController::update', ['filter' => 'admin']);
$routes->get('manager/seo', 'Manager\SeoController::index', ['filter' => 'admin']);
$routes->post('manager/seo', 'Manager\SeoController::update', ['filter' => 'admin']);
