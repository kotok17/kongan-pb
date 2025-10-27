<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Test route untuk debugging
$routes->get('test', function () {
  echo "<h1>Routing Test Works!</h1>";
  echo "<p>Base URL: " . base_url() . "</p>";
  echo "<p>Current URL: " . current_url() . "</p>";
  echo "<hr>";
  echo "<h3>Available Routes:</h3>";
  echo "<ul>";
  echo "<li><a href='" . base_url() . "'>Home</a></li>";
  echo "<li><a href='" . base_url('login') . "'>Login</a></li>";
  echo "<li><a href='" . base_url('dashboard/admin') . "'>Dashboard Admin</a></li>";
  echo "<li><a href='" . base_url('dashboard/anggota') . "'>Dashboard Anggota</a></li>";
  echo "</ul>";
});

// Default route
$routes->get('/', 'Home::index');

// Auth routes
$routes->match(['get', 'post'], 'login', 'AuthController::index');
$routes->post('login/process', 'AuthController::login');
$routes->get('logout', 'AuthController::logout');

// Dashboard routes - tanpa filter dulu untuk testing
$routes->get('dashboard', 'DashboardController::index');
$routes->get('dashboard/admin', 'DashboardController::admin');
$routes->get('dashboard/anggota', 'DashboardController::anggota');

// Kegiatan routes
$routes->group('kegiatan', ['filter' => 'auth'], function ($routes) {
  $routes->get('/', 'KegiatanController::index');
  $routes->get('tambah', 'KegiatanController::tambah');
  $routes->post('simpan', 'KegiatanController::simpan');
  $routes->get('detail/(:num)', 'KegiatanController::detail/$1');
  $routes->get('edit/(:num)', 'KegiatanController::edit/$1');
  $routes->post('update/(:num)', 'KegiatanController::update/$1');
  $routes->delete('hapus/(:num)', 'KegiatanController::hapus/$1');
  $routes->post('tambah_kongan', 'KegiatanController::tambah_kongan');
  $routes->match(['delete', 'post'], 'hapus_kongan/(:num)', 'KegiatanController::hapus_kongan/$1');
});

// Anggota routes
$routes->group('anggota', function ($routes) {
  $routes->get('/', 'AnggotaController::index');
  $routes->get('tambah', 'AnggotaController::tambah');
  $routes->post('simpan', 'AnggotaController::simpan');
  $routes->get('edit/(:num)', 'AnggotaController::edit/$1');
  $routes->post('update/(:num)', 'AnggotaController::update/$1');
  $routes->delete('hapus/(:num)', 'AnggotaController::hapus/$1');
});

// Undangan routes
$routes->group('undangan', function ($routes) {
  $routes->get('preview/(:num)', 'UndanganController::preview/$1');
  $routes->get('generate/(:num)', 'UndanganController::generate/$1');
});

// Routes Forgot Password
$routes->get('forgot-password', 'ForgotPasswordController::index');
$routes->post('forgot-password/process', 'ForgotPasswordController::process');

// Tambahkan route ini
$routes->get('api/dashboard/stats', 'DashboardController::getStats');

// User management routes (admin only)
$routes->group('users', ['filter' => 'auth'], function ($routes) {
  $routes->post('reset-password/(:num)', 'UserController::resetPassword/$1');
  $routes->delete('delete/(:num)', 'UserController::delete/$1');
});
