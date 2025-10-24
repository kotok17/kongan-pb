<?php
// filepath: app/Filters/AnggotaFilter.php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AnggotaFilter implements FilterInterface
{
  public function before(RequestInterface $request, $arguments = null)
  {
    // Cek apakah user sudah login
    if (!session()->get('logged_in')) {
      return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu!');
    }

    // Cek apakah user adalah anggota atau admin (admin bisa akses semua)
    if (!in_array(session()->get('role'), ['anggota', 'admin'])) {
      return redirect()->to('/login')->with('error', 'Akses ditolak!');
    }
  }

  public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
  {
    // Do something here
  }
}
