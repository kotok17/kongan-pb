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
$routes->get('/', 'AuthController::login');

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
  // List & Detail
  $routes->get('/', 'KegiatanController::index');
  $routes->get('detail/(:num)', 'KegiatanController::detail/$1');

  // CRUD Kegiatan
  $routes->get('tambah', 'KegiatanController::tambah_kegiatan');
  $routes->post('simpan', 'KegiatanController::simpan');
  $routes->get('edit/(:num)', 'KegiatanController::edit/$1');
  $routes->post('update/(:num)', 'KegiatanController::update/$1');
  $routes->match(['post', 'delete'], 'hapus/(:num)', 'KegiatanController::hapus/$1');

  // Kongan Management
  $routes->post('tambah_kongan', 'KegiatanController::tambah_kongan');
  $routes->match(['post', 'delete'], 'hapus_kongan/(:num)', 'KegiatanController::hapus_kongan/$1');
  $routes->post('import_kongan', 'KegiatanController::import_kongan');
  $routes->post('update_pengaturan/(:num)', 'KegiatanController::update_pengaturan/$1');

  // Template & Export
  $routes->get('download_template_import', 'KegiatanController::download_template_import');
  $routes->get('export_pdf/(:num)', 'KegiatanController::export_pdf/$1');
  $routes->get('export_excel/(:num)', 'KegiatanController::export_excel/$1');
});

// Anggota routes
$routes->group('anggota', ['filter' => 'auth'], function ($routes) {
  $routes->get('/', 'AnggotaController::index');
  $routes->get('tambah', 'AnggotaController::tambah');
  $routes->post('simpan', 'AnggotaController::simpan');
  $routes->get('edit/(:num)', 'AnggotaController::edit/$1');
  $routes->post('update/(:num)', 'AnggotaController::update/$1');
  $routes->delete('hapus/(:num)', 'AnggotaController::hapus/$1');
});

// Undangan routes
$routes->group('undangan', ['filter' => 'auth'], function ($routes) {
  $routes->get('preview/(:num)', 'UndanganController::preview/$1');
  $routes->get('generate/(:num)', 'UndanganController::generate/$1');
});

// Routes Forgot Password
$routes->get('forgot-password', 'ForgotPasswordController::index');
$routes->post('forgot-password/process', 'ForgotPasswordController::process');

// API Routes
$routes->get('api/dashboard/stats', 'DashboardController::getStats');

// User management routes (admin only)
$routes->group('users', ['filter' => 'auth'], function ($routes) {
  $routes->post('reset-password/(:num)', 'UserController::resetPassword/$1');
  $routes->delete('delete/(:num)', 'UserController::delete/$1');
});