<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Auth
$routes->get('/login', 'AuthController::index');
$routes->post('/login', 'AuthController::login');
$routes->get('/logout', 'AuthController::logout');

// Dashboard
$routes->get('/dashboard/admin', 'DashboardController::admin');
$routes->get('/dashboard/anggota', 'DashboardController::anggota');

// Anggota
$routes->get('/anggota', 'AnggotaController::index');
$routes->post('/anggota/tambah_anggota', 'AnggotaController::tambah_anggota');
$routes->post('/anggota/import', 'AnggotaController::import');
$routes->delete('/anggota/hapus/(:num)', 'AnggotaController::hapus/$1');

// Kegiatan
$routes->get('/kegiatan', 'KegiatanController::index');
$routes->post('/kegiatan/tambah_kegiatan', 'KegiatanController::tambah_kegiatan');
$routes->get('/kegiatan/detail/(:num)', 'KegiatanController::detail/$1');
$routes->delete('/kegiatan/hapus/(:num)', 'KegiatanController::hapus/$1');

// Kongan
$routes->post('/kegiatan/tambah_kongan', 'KegiatanController::tambah_kongan');
$routes->post('/kegiatan/import_kongan', 'KegiatanController::import_kongan');
$routes->delete('/kegiatan/hapus_kongan/(:num)', 'KegiatanController::hapus_kongan/$1');
