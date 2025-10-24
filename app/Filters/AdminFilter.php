<?php
// filepath: app/Filters/AdminFilter.php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AdminFilter implements FilterInterface
{
  public function before(RequestInterface $request, $arguments = null)
  {
    // Cek apakah user sudah login
    if (!session()->get('logged_in')) {
      return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu!');
    }

    // Cek apakah user adalah admin
    if (session()->get('role') !== 'admin') {
      return redirect()->to('/login')->with('error', 'Akses ditolak! Anda bukan admin.');
    }
  }

  public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
  {
    // Do something here
  }
}
