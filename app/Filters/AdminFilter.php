<?php
// filepath: app/Filters/AdminFilter.php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminFilter implements FilterInterface
{
  public function before(RequestInterface $request, $arguments = null)
  {
    // Cek apakah user sudah login
    if (!session()->get('logged_in')) {
      session()->setFlashdata('error', 'Anda harus login terlebih dahulu!');
      return redirect()->to('/login');
    }

    // Cek apakah user adalah admin
    if (session()->get('role') !== 'admin') {
      session()->setFlashdata('error', 'Akses ditolak! Hanya admin yang diizinkan.');
      return redirect()->back();
    }
  }

  public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
  {
    // Do something here
  }
}
