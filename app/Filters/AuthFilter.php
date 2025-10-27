<?php
// filepath: app/Filters/AuthFilter.php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
  public function before(RequestInterface $request, $arguments = null)
  {
    // Cek apakah user sudah login
    if (!session()->get('logged_in')) {
      // Debug session (hapus setelah testing)
      // log_message('debug', 'Session data: ' . print_r(session()->get(), true));

      return redirect()->to('/login')
        ->with('error', 'Silakan login terlebih dahulu');
    }

    // Cek apakah session masih valid
    $role = session()->get('role');
    if (empty($role)) {
      // Session corrupt, hapus dan redirect
      session()->destroy();
      return redirect()->to('/login')
        ->with('error', 'Session tidak valid, silakan login ulang');
    }
  }

  public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
  {
    // Tidak perlu action setelah request
  }
}
